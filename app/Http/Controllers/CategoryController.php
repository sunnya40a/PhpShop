<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Inventory;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreCategory;
use App\Http\Requests\UpdateCategory;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class CategoryController extends Controller
{
    //below function is for routing handler for 
    // Route::get('/categories/list', [CategoryController::class, 'index']);
    // Route::get('/categories/list/{code}', [CategoryController::class, 'show']);
    public function handlelist(Request $request)
    {
        // Retrieve the PO parameter from the query parameters in the request
        $code = $request->query('code');

        if ($code) {
            // If PO parameter is present, show the purchase with that PO
            return $this->show($request);
        } else {
            // If PO parameter is not present, list all purchases
            return $this->index($request);
        }
    }
    //============================================================================================//
    // API endpoint for showing a list of categories

    public function index(Request $req)
    {
        // Retrieve request parameters with validation
        $validatedData = $req->validate([
            'page' => 'integer|min:1',
            'limit' => 'integer|min:1|max:100',
            'sortBy' => 'string|in:category_code,description',
            'sortOrder' => 'string|in:asc,desc',
            'search' => 'string|max:255|nullable',
        ]);

        // Sanitize search term
        $searchTerm = sanitizeSearchText($validatedData['search'] ?? '');

        // Validate request parameters
        $page = $validatedData['page'] ?? 1; // Default page is 1
        $limit = $validatedData['limit'] ?? 1000; // Default limit is 10
        $sortBy = $validatedData['sortBy'] ?? 'category_code'; // Default sort by 'category_code'
        $sortOrder = $validatedData['sortOrder'] ?? 'asc'; // Default sort order is 'asc'

        // Query the records with optional filters
        $query = Category::query();

        // Apply search filter if a search term is provided
        if ($searchTerm !== '') {
            $query->where(function ($q) use ($searchTerm) {
                $q->where(
                    'category_code',
                    'like',
                    '%' . $searchTerm . '%'
                )
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // Get the total record count before applying pagination
        $totalCount = $query->count();

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        // Apply pagination
        $query->skip(($page - 1) * $limit)
            ->take($limit);

        // Retrieve the records
        $categories = $query->get();

        // Return the categories as a collection with total count
        return new CategoryCollection($categories, $totalCount);
    }
    // End of API endpoint for showing category list

    //============================================================================================//

    // API endpoint for showing a specific category
    public function show(Request $req)
    {
        // Retrieve the code query parameter from the request
        $code = $req->query('code');

        // Validate the code parameter
        if (!$code) {
            // Return a 400 Bad Request response if the code parameter is missing
            return response()->json([
                "error" => "The code parameter is required"
            ], 400);
        }

        // Find the category with the specified category_code
        $category = Category::where('category_code', $code)->first();

        // Check if the category exists
        if (!$category) {
            // Return a 404 Not Found response if no category is found
            return response()->json([
                "error" => "No category found for code: {$code}"
            ], 404);
        }

        // Return the category as a resource
        return new CategoryResource($category);
    }
    // End of API endpoint for showing a specific category


    //============================================================================================//
    // API endpoint to save a new category
    public function store(StoreCategory $request)
    {
        DB::beginTransaction();
        try {
            // Create a new Category instance with validated data from the request
            $category = Category::create([
                'category_code' => $request->category_code,
                'description' => $request->description,
            ]);

            // Commit the transaction
            DB::commit();

            // Retrieve the saved record with fresh data
            $savedRecord = $category->fresh();

            // Return a success response
            return response()->json([
                "message" => "Category created successfully.",
                "data" => new CategoryResource($savedRecord),
            ], 201);
        } catch (QueryException $e) {
            // Roll back the transaction in case of a database error
            DB::rollback();

            // Check for duplicate entry error code (MySQL error code 1062)
            if ($e->errorInfo[1] == 1062) {
                return response()->json(['error' => 'Duplicate entry: A category with the same category code already exists.'], 400);
            }

            // Log the database error with more context
            Log::error("Failed to save new category. Database error: {$e->getMessage()}");

            // Return a database error response
            return response()->json(['error' => 'Database error while saving category'], 500);
        } catch (\Exception $e) {
            // Roll back the transaction in case of other errors
            DB::rollback();

            // Log the generic error with more context
            Log::error("Failed to save new category. Error: {$e->getMessage()}");

            // Return an error response
            return response()->json(['error' => 'Failed to save new category'], 500);
        }
    }
    // End of store function.
    //============================================================================================//

    // Api end point to update category.
    public function update(UpdateCategory $request)
    {
        // Retrieve the category code from the query parameters
        $code = $request->query('code');

        // Validate the code parameter
        if (!$code) {
            // Return a 400 Bad Request response if the code parameter is missing
            return response()->json([
                "error" => "The code parameter is required"
            ], 400);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Find the category by code
            $category = Category::where('category_code', $code)->firstOrFail();

            // Update the category description
            $category->update([
                'description' => $request->description,
            ]);

            // Commit the transaction
            DB::commit();

            // Return the updated category data
            return response()->json([
                'message' => 'Record updated successfully.',
                'data' => [
                    'category_code' => $category->category_code,
                    'description' => $category->description,
                ],
            ], 200);
        } catch (ModelNotFoundException $exception) {
            // Roll back the transaction if category not found
            DB::rollback();
            return response()->json([
                'message' => 'Record not found with code: ' . $code,
            ], 404);
        } catch (QueryException $e) {
            // Roll back the transaction in case of a database error
            DB::rollback();

            // Check for duplicate entry error code (MySQL error code 1062)
            if ($e->errorInfo[1] == 1062) {
                return response()->json(['error' => 'Duplicate entry: A category with the same data already exists.'], 400);
            }

            // Log the database error with more context
            Log::error('Failed to update category. Code: ' . $code . '. Database error: ' . $e->getMessage());

            // Return a database error response
            return response()->json(['message' => 'Database error while updating category.'], 500);
        } catch (\Exception $e) {
            // Roll back the transaction on error
            DB::rollback();
            Log::error('Failed to update category. Code: ' . $code . '. Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update category.'
            ], 500);
        }
    } // End of update function.
    //============================================================================================//


    // Api endpoint to delete a category
    public function destroy(Request $request)
    {
        // Retrieve the code parameter from the request
        $code = $request->query('code');

        // Validate that the code parameter is provided
        if (!$code) {
            return response()->json(
                ["error" => "The 'code' parameter is required"],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Begin a transaction
        DB::beginTransaction();
        try {
            // Retrieve the category record with the specified code
            $category = Category::where('category_code', $code)->first();

            // Check if the category record exists
            if (!$category) {
                // Rollback the transaction if category not found
                DB::rollback();
                return response()->json([
                    "error" => "Category with code '{$code}' not found"
                ], Response::HTTP_NOT_FOUND);
            }

            // Check for dependencies in the inventory table
            // Look for inventory items where the 'Item_list' field starts with the category code
            $inventory = Inventory::where('Item_list', 'like', $category->category_code . '%')->first();

            // If there is a matching inventory record, the category cannot be deleted
            if ($inventory) {
                // Rollback the transaction
                DB::rollback();
                // Return an error response indicating dependency in the inventory
                return response()->json([
                    "error" => "Unable to delete category. Items referencing this category exist in the inventory."
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Delete the category record
            $category->delete();

            // Commit the transaction if everything is successful
            DB::commit();

            // Return a success response
            return response()->json([
                "message" => "Category with code '{$category->category_code}' successfully deleted"
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollback();
            // Log the error for debugging purposes
            Log::error("Error while deleting category with code '{$code}': " . $e->getMessage());
            // Handle exceptions and return an error response
            return response()->json([
                "error" => "An error occurred while deleting the category: " . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    // End of destroy function.
}
