<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplier;
use App\Http\Requests\UpdateSupplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Supplier;
use App\Models\PurchaseHistory;
use App\Models\Inventory;
use App\Http\Resources\SupplierResource;
use App\Http\Resources\SupplierCollection;
use Illuminate\Database\QueryException;

class SuppliersController extends Controller
{
    /**
     * Fetch a list of suppliers for a dropdown.
     */
    public function dropdownlist(Request $req)
    {
        try {
            // Query the records and get the unique item_list and description values
            $suppliers = Supplier::select('id', 's_name')->distinct()->orderBy('id')->get();

            // Return the unique item_list and description values as JSON
            return new SupplierCollection($suppliers);
        } catch (QueryException $e) {
            Log::error('Database query error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching the suppliers list.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function handlesupplier(Request $request)
    {
        // Retrieve the Id parameter from the query parameters in the request
        $id = $request->query('id');

        if ($id) {
            // If Id parameter is present, show the supplier with that id
            return $this->show($request);
        } else {
            // If id parameter is not present, list all suppliers
            return $this->index($request);
        }
    }

    /**
     * Fetch a detailed list of suppliers.
     */

    ///Old code above and new code below.
    // API endpoint for showing a list of Supplier
    public function index(Request $req)
    {
        // Retrieve request parameters with validation
        $validatedData = $req->validate([
            'page' => 'integer|min:1',
            'limit' => 'integer|min:1|max:100',
            'sortBy' => 'string|in:id,s_name,mobile1,mobile2,c_person',
            'sortOrder' => 'string|in:asc,desc',
            'search' => 'string|max:255|nullable',
        ]);

        // Sanitize search term
        $searchTerm = sanitizeSearchText($validatedData['search'] ?? '');

        // Validate request parameters
        $page = $validatedData['page'] ?? 1; // Default page is 1
        $limit = $validatedData['limit'] ?? 10; // Default limit is 10
        $sortBy = $validatedData['sortBy'] ?? 's_name'; // Default sort by 's_name'
        $sortOrder = $validatedData['sortOrder'] ?? 'asc'; // Default sort order is 'asc'

        // Query the records with optional filters
        $query = Supplier::query();

        // Apply search filter if a search term is provided

        if ($searchTerm !== '') {
            $query->where(function ($q) use ($searchTerm) {
                $q->where(
                    'id',
                    'like',
                    '%' . $searchTerm . '%'
                )
                    ->orWhere('s_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('mobile1', 'like', '%' . $searchTerm . '%')
                    ->orWhere('mobile2', 'like', '%' . $searchTerm . '%')
                    ->orWhere('c_person', 'like', '%' . $searchTerm . '%')
                    ->orWhere('contact_info', 'like', '%' . $searchTerm . '%');
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
        $suppliers = $query->get();

        // Return the suppliers as a collection with total count
        return new SupplierCollection($suppliers, $totalCount);
    }

    ///////////////////////////////////////////////////////////////////////////////////

    // API endpoint for showing a specific suppliers
    public function show(Request $req)
    {
        // Retrieve the id query parameter from the request
        $id = $req->query('id');

        // Validate the id parameter
        if (!$id) {
            // Return a 400 Bad Request response if the id parameter is missing
            return response()->json([
                "error" => "The id parameter is required"
            ], 400);
        }

        // Find the supplier with the specified id
        $suppliers = Supplier::where('id', $id)->first();

        // Check if the id is exists
        if (!$suppliers) {
            // Return a 404 Not Found response if no suppliers is found
            return response()->json([
                "error" => "No suppler found for id: {$id}"
            ], 404);
        }

        // Return the suppliers as a resource
        return new SupplierResource($suppliers);
    }
    // End of API endpoint for showing a specific supplier
    //============================================================================================//

    // API endpoint to save a new supplier
    public function store(StoreSupplier $request)
    {
        DB::beginTransaction();

        try {
            // Create a new supplier instance with validated data from the request
            $suppler = Supplier::create([
                's_name' => $request->s_name,
                'mobile1' => $request->mobile1,
                'mobile2' => $request->mobile2,
                'c_person' => $request->c_person,
                'contact_info' => $request->contact_info,
            ]);

            // Commit the transaction
            DB::commit();

            // Retrieve the saved record with fresh data
            $savedRecord = $suppler->fresh();

            // Return a success response
            return response()->json([
                "message" => "New supplier [{$request->s_name}] created successfully.",
                "data" => new SupplierResource($savedRecord),
            ], 201);
        } catch (QueryException $e) {
            // Roll back the transaction in case of a database error
            DB::rollback();

            // Check for duplicate entry error code (MySQL error code 1062)
            if (
                $e->errorInfo[1] == 1062
            ) {
                return response()->json(['error' => 'Duplicate entry: id with the same data already exists.'], 400);
            }

            // Log the database error with more context
            Log::error("Failed to save new supplier. Database error: {$e->getMessage()}");

            // Return a database error response
            return response()->json(['error' => 'Database error while saving new supplier'], 500);
        } catch (\Exception $e) {
            // Roll back the transaction in case of other errors
            DB::rollback();

            // Log the generic error with more context
            Log::error("Failed to save new supplier. Error: {$e->getMessage()}");

            // Return an error response
            return response()->json(['error' => 'Failed to save new supplier.'], 500);
        }
    }
    // End of store function.
    //============================================================================================//

    // Api end point to update Supplier.
    public function update(UpdateSupplier $request)
    {
        // Retrieve the Supplier id from the query parameters
        $id = $request->query('id');

        // Validate the id parameter
        if (!$id) {
            // Return a 400 Bad Request response if the id parameter is missing
            return response()->json([
                "error" => "The Supplier id parameter is required."
            ], 400);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Find the Supplier by id
            $suppler = Supplier::where('id', $id)->firstOrFail();

            // Update the Supplier fields
            $suppler->update([
                's_name' => $request->s_name,
                'mobile1' => $request->mobile1,
                'mobile2' => $request->mobile2,
                'c_person' => $request->c_person,
                'contact_info' => $request->contact_info,
            ]);

            // Commit the transaction
            DB::commit();

            // Return the updated Supplier data
            return response()->json([
                'message' => 'Supplier updated successfully.',
                'data' => [
                    'id' => $suppler->id,
                    's_name' => $suppler->s_name,
                    'mobile1' => $suppler->mobile1,
                    'mobile2' => $suppler->mobile2,
                    'c_person' => $suppler->c_person,
                    'contact_info' => $suppler->contact_info,
                ],
            ], 200);
        } catch (ModelNotFoundException $exception) {
            // Roll back the transaction if Supplier not found
            DB::rollback();
            return response()->json([
                'error' => 'Supplier not found with id: ' . $id,
            ], 404);
        } catch (QueryException $e) {
            // Roll back the transaction in case of a database error
            DB::rollback();

            // Check for duplicate entry error code (MySQL error code 1062)
            if ($e->errorInfo[1] == 1062) {
                return response()->json(['error' => 'Duplicate entry: Supplier with the same name already exists.'], 400);
            }

            // Log the database error with more context
            Log::error('Failed to update Supplier. Id: ' . $id . '. Database error: ' . $e->getMessage());

            // Return a database error response
            return response()->json(['error' => 'Database error while updating supplier.'], 500);
        } catch (\Exception $e) {
            // Roll back the transaction on error
            DB::rollback();
            Log::error('Failed to update Supplier. Id: ' . $id . '. Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update Supplier.'
            ], 500);
        }
    } // End of update function.
    //============================================================================================//

    //============================================================================================//

    public function destroy(Request $request)
    {
        // Retrieve the id parameter from the request
        $id = $request->query('id');

        // Validate that the id parameter is provided
        if (!$id) {
            return response()->json(
                ["error" => "The supplier 'id' parameter is required"],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Begin a transaction
        DB::beginTransaction();
        try {
            // Retrieve the inventory record with the specified id
            $supplier = Supplier::where('id', $id)->first();

            // Check if the inventory record exists
            if (!$supplier) {
                // Rollback the transaction if inventory not found
                DB::rollback();
                return response()->json([
                    "error" => "Supplier with id '{$id}' not found"
                ], Response::HTTP_NOT_FOUND);
            }


            // Check for dependencies in the Inventory table
            $inventory = Inventory::where('supplier_id', 'like', $id . '%')->first();

            // If there is a matching inventory with this supplier, the supplier cannot be deleted
            if ($inventory) {
                // Rollback the transaction
                DB::rollback();
                // Return an error response indicating dependency in the Inventory table
                return response()->json([
                    "error" => "Unable to delete supplier ['{$supplier->s_name}'(ID:'{$id}')] because there are items in the Inventory Table that reference this supplier.  Please address these items before attempting to delete the supplier."
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } {
            }

            // Check for dependencies in the purchase history table
            $purchaseHistory = PurchaseHistory::where('supplier_id', 'like', $id . '%')->first();

            // If there is a matching purchase history record, the inventory cannot be deleted
            if ($purchaseHistory) {
                // Rollback the transaction
                DB::rollback();
                // Return an error response indicating dependency in the purchase history
                return response()->json([
                    "error" => "Unable to delete supplier ['{$supplier->s_name}'(ID:'{$id}')] because there are items in the Purchase History Table that reference this supplier.  Please address these items before attempting to delete the supplier."
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Delete the inventory record
            $supplier->delete();

            // Commit the transaction if everything is successful
            DB::commit();

            // Return a success response
            return response()->json([
                "message" => "Supplier '{$supplier->s_name}' with id '{$id}' successfully deleted"
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollback();
            // Log the error for debugging purposes
            Log::error("Error while deleting Supplier with id '{$id}': " . $e->getMessage());
            // Handle exceptions and return an error response
            return response()->json([
                "error" => "An error occurred while deleting the ['{$id}'] Supplier: " . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
