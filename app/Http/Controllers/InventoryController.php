<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventory;
use App\Http\Requests\UpdateInventory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Inventory;
use App\Models\PurchaseHistory;
use App\Models\Category;
use App\Http\Resources\InventoryResource;
use App\Http\Resources\InventoryCollection;
use App\Models\SalesHistory;
use App\Models\Supplier;
use Illuminate\Database\QueryException;

class InventoryController extends Controller
{
    public function handleInventory(Request $request)
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
    // API endpoint for showing a list of inventory
    public function index(Request $req)
    {
        // Retrieve request parameters with validation
        $validatedData = $req->validate([
            'page' => 'integer|min:1',
            'limit' => 'integer|min:1|max:100',
            'sortBy' => 'string|in:item_list,description,qty,unit,category',
            'sortOrder' => 'string|in:asc,desc',
            'search' => 'string|max:255|nullable',
        ]);

        // Sanitize search term
        $searchTerm = sanitizeSearchText($validatedData['search'] ?? '');

        // Validate request parameters
        $page = $validatedData['page'] ?? 1; // Default page is 1
        $limit = $validatedData['limit'] ?? 10; // Default limit is 10
        $sortBy = $validatedData['sortBy'] ?? 'item_list'; // Default sort by 'item_list'
        $sortOrder = $validatedData['sortOrder'] ?? 'asc'; // Default sort order is 'asc'

        // Query the records with optional filters
        $query = Inventory::leftJoin('suppliers', 'inventory.supplier_id', '=', 'suppliers.id')
            ->select('inventory.item_list', 'inventory.description', 'inventory.qty', 'inventory.unit', 'inventory.category', 'inventory.supplier_id', 'suppliers.s_name');


        // Apply search filter if a search term is provided
        if ($searchTerm !== '') {
            $query->where(function ($q) use ($searchTerm) {
                $q->where(
                    'item_list',
                    'like',
                    '%' . $searchTerm . '%'
                )
                    ->orWhere('description', 'like', '%' . $searchTerm . '%')
                    ->orWhere('qty', 'like', '%' . $searchTerm . '%')
                    ->orWhere('unit', 'like', '%' . $searchTerm . '%')
                    ->orWhere('category', 'like', '%' . $searchTerm . '%');
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
        $inventories = $query->get();

        // Return the categories as a collection with total count
        return new InventoryCollection($inventories, $totalCount);
    }
    // End of API endpoint for showing inventory list

    //============================================================================================//


    // API endpoint for showing a specific inventory
    public function show(Request $req)
    {
        // Retrieve the code query parameter from the request
        $item_list = $req->query('code');

        // Validate the code parameter
        if (!$item_list) {
            // Return a 400 Bad Request response if the code parameter is missing
            return response()->json([
                "error" => "The code parameter is required"
            ], 400);
        }

        // Find the inventory with the specified item_list
        //$inventories = Inventory::where('item_list', $item_list)->first();
        $inventories = Inventory::leftJoin('suppliers', 'inventory.supplier_id', '=', 'suppliers.id')
            ->select('inventory.item_list', 'inventory.description', 'inventory.qty', 'inventory.unit', 'inventory.category', 'inventory.supplier_id', 'suppliers.s_name')
            ->where('inventory.item_list', '=', $item_list)
            ->first();


        // Check if the inventory exists
        if (!$inventories) {
            // Return a 404 Not Found response if no inventory is found
            return response()->json([
                "error" => "No inventory found for code: {$item_list}"
            ], 404);
        }

        // Return the inventory as a resource
        return new InventoryResource($inventories);
    }
    // End of API endpoint for showing a specific inventory

    //============================================================================================//
    // API endpoint to save a new Inventory
    public function store(StoreInventory $request)
    {
        DB::beginTransaction();

        try {
            // Create a new Inventory instance with validated data from the request
            $inventory = Inventory::create([
                'item_list' => $request->item_list,
                'description' => $request->description,
                'qty' => $request->qty,
                'unit' => $request->unit,
                'supplier_id' => $request->supplier_id,
                'category' => $request->category,
            ]);

            // Commit the transaction
            DB::commit();

            // Retrieve the saved record with fresh data
            $savedRecord = $inventory->fresh();

            // Return a success response
            return response()->json([
                "message" => "New inventory item [{$request->item_list}] created successfully.",
                "data" => new InventoryResource($savedRecord),
            ], 201);
        } catch (QueryException $e) {
            // Roll back the transaction in case of a database error
            DB::rollback();

            // Check for duplicate entry error code (MySQL error code 1062)
            if (
                $e->errorInfo[1] == 1062
            ) {
                return response()->json(['error' => 'Duplicate entry: Inventory Code with the same data already exists.'], 400);
            }

            // Log the database error with more context
            Log::error("Failed to save new inventory item. Database error: {$e->getMessage()}");

            // Return a database error response
            return response()->json(['error' => 'Database error while saving new inventory'], 500);
        } catch (\Exception $e) {
            // Roll back the transaction in case of other errors
            DB::rollback();

            // Log the generic error with more context
            Log::error("Failed to save new inventory item. Error: {$e->getMessage()}");

            // Return an error response
            return response()->json(['error' => 'Failed to save new inventory item.'], 500);
        }
    }
    // End of store function.
    //============================================================================================//

    // Api end point to update Inventory.
    public function update(UpdateInventory $request)
    {
        // Retrieve the Inventory code from the query parameters
        $code = $request->query('code');

        // Validate the code parameter
        if (!$code) {
            // Return a 400 Bad Request response if the code parameter is missing
            return response()->json([
                "error" => "The code parameter is required."
            ], 400);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Find the Inventory by code
            $inventory = Inventory::where('item_list', $code)->firstOrFail();

            // Update the Inventory fields except item_list
            $inventory->update([
                'description' => $request->description,
                'qty' => $request->qty ?? $inventory->qty, // Keep the existing qty if not provided
                'unit' => $request->unit,
                'supplier_id' => $request->supplier_id,
                'category' => $inventory->category,
            ]);

            // Commit the transaction
            DB::commit();

            // Return the updated Inventory data
            return response()->json([
                'message' => 'Inventory updated successfully.',
                'data' => [
                    'item_list' => $inventory->item_list,
                    'description' => $inventory->description,
                    'qty' => $inventory->qty,
                    'unit' => $inventory->unit,
                    'supplier_id' => $inventory->supplier_id,
                    'category' => $inventory->category,
                ],
            ], 200);
        } catch (ModelNotFoundException $exception) {
            // Roll back the transaction if Inventory not found
            DB::rollback();
            return response()->json([
                'error' => 'Inventory not found with code: ' . $code,
            ], 404);
        } catch (QueryException $e) {
            // Roll back the transaction in case of a database error
            DB::rollback();

            // Check for duplicate entry error code (MySQL error code 1062)
            if ($e->errorInfo[1] == 1062) {
                return response()->json(['error' => 'Duplicate entry: An inventory with the same data already exists.'], 400);
            }

            // Log the database error with more context
            Log::error('Failed to update inventory. Code: ' . $code . '. Database error: ' . $e->getMessage());

            // Return a database error response
            return response()->json(['error' => 'Database error while updating inventory.'], 500);
        } catch (\Exception $e) {
            // Roll back the transaction on error
            DB::rollback();
            Log::error('Failed to update inventory. Code: ' . $code . '. Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update inventory.'
            ], 500);
        }
    } // End of update function.
    //============================================================================================//

    public function destroy(Request $request)
    {
        // Retrieve the code parameter from the request
        $item_list = $request->query('code');

        // Validate that the code parameter is provided
        if (!$item_list) {
            return response()->json(
                ["error" => "The 'code' parameter is required"],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Begin a transaction
        DB::beginTransaction();
        try {
            // Retrieve the inventory record with the specified code
            $inventory = Inventory::where('item_list', $item_list)->first();

            // Check if the inventory record exists
            if (!$inventory) {
                // Rollback the transaction if inventory not found
                DB::rollback();
                return response()->json([
                    "error" => "Inventory with code '{$item_list}' not found"
                ], Response::HTTP_NOT_FOUND);
            }

            // Check for dependencies in the purchase history table
            $purchaseHistory = PurchaseHistory::where('item_list', 'like', $item_list . '%')->first();

            // If there is a matching purchase history record, the inventory cannot be deleted
            if ($purchaseHistory) {
                // Rollback the transaction
                DB::rollback();
                // Return an error response indicating dependency in the purchase history
                return response()->json([
                    "error" => "Unable to delete inventory['{$item_list}']. Items referencing this inventory exist in the Purchase History."
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Check for dependencies in the sales history table
            $salesHistory = SalesHistory::where(
                'item_list',
                'like',
                $item_list . '%'
            )->first();

            // If there is a matching sales history record, the inventory cannot be deleted
            if ($salesHistory) {
                // Rollback the transaction
                DB::rollback();
                // Return an error response indicating dependency in the sales history
                return response()->json([
                    "error" => "Unable to delete inventory['{$item_list}']. Items referencing this inventory exist in the Sales History."
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Delete the inventory record
            $inventory->delete();

            // Commit the transaction if everything is successful
            DB::commit();

            // Return a success response
            return response()->json([
                "message" => "Inventory with code '{$item_list}' successfully deleted"
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollback();
            // Log the error for debugging purposes
            Log::error("Error while deleting inventory with code '{$item_list}': " . $e->getMessage());
            // Handle exceptions and return an error response
            return response()->json([
                "error" => "An error occurred while deleting the ['{$item_list}']inventory: " . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // End of destroy function.
    //============================================================================================//

    public function store1(StoreInventory $request)
    {
        DB::beginTransaction();
        try {
            // Retrieve category description from the request
            $categoryDescription = $request->input('category');
            $itemList = $request->input('item_list'); // New input for item_list
            $description = $request->input('description');
            $supplierid = $request->input('supplier_id');
            $unit = $request->input('unit');

            // Find the category code based on the description
            $categoryCode = Category::where('description', $categoryDescription)->value('category_code');

            // Check if the category exists
            if (!$categoryCode) {
                Log::warning("Category '{$categoryDescription}' not found in category table.");
                return response()->json([
                    "error" => "Category '{$categoryDescription}' not found in category table."
                ], Response::HTTP_NOT_FOUND);
            }

            // Handle item_list validation if provided
            if ($itemList) {
                // Validate if the provided item_list is in the correct format
                $validItemList = preg_match('/^' . $categoryCode . ' - \d{3}$/', $itemList);

                if (!$validItemList) {
                    Log::warning("Invalid item_list '{$itemList}' provided.");
                    return response()->json([
                        "error" => "The provided item_list '{$itemList}' is not in the correct format."
                    ], Response::HTTP_BAD_REQUEST);
                }

                // Check if the provided item_list already exists
                $existingInventory = Inventory::where('item_list', $itemList)->exists();
                if ($existingInventory) {
                    Log::warning("Item_list '{$itemList}' already exists.");
                    return response()->json([
                        "error" => "Item_list '{$itemList}' already exists."
                    ], Response::HTTP_CONFLICT);
                }

                // Use the provided item_list
                $newItemListCode = $itemList;
            } else {
                // Generate a new item_list code
                $latestInventory = Inventory::where('item_list', 'like', $categoryCode . ' - %')
                    ->orderBy('item_list', 'desc')
                    ->first();

                // Extract the numeric part and increment it
                if ($latestInventory) {
                    $latestCode = $latestInventory->item_list;
                    $latestNumber = (int) substr($latestCode, -3); // Get the last 3 digits as an integer
                    $newNumber = str_pad($latestNumber + 1, 3, '0', STR_PAD_LEFT); // Increment and format as 3 digits
                } else {
                    // If no inventory exists for this category, start with '001'
                    $newNumber = '001';
                }

                // Format the new item_list code
                $newItemListCode = "{$categoryCode} - {$newNumber}";
            }

            // Validate the required fields are not null
            if (!$description) {
                return response()->json([
                    "error" => "The description field is required."
                ], Response::HTTP_BAD_REQUEST);
            }

            // Validate the required fields are not null
            if (!$supplierid) {
                return response()->json([
                    "error" => "The supplier field is required."
                ], Response::HTTP_BAD_REQUEST);
            }

            if (!$unit) {
                return response()->json([
                    "error" => "The unit field is required."
                ], Response::HTTP_BAD_REQUEST);
            }

            // Create a new Inventory instance with validated data from the request
            $inventory = Inventory::create([
                'item_list' => $newItemListCode,
                'description' => $request->description,
                //'qty' => $request->qty ?? 0,
                'qty' => 0,
                'unit' => $request->unit,
                'supplier_id' => $request->supplier_id,
                'category' => $request->category,
            ]);

            // Commit the transaction
            DB::commit();

            // Retrieve the saved record with fresh data
            $savedRecord = $inventory->fresh();

            // Return a success response
            return response()->json([
                "message" => "Inventory ['{$newItemListCode}'] created successfully.",
                "data" => $savedRecord,
            ], Response::HTTP_CREATED);
        } catch (QueryException $e) {
            // Roll back the transaction in case of a database error
            DB::rollback();

            // Check for duplicate entry error code (MySQL error code 1062)
            if ($e->errorInfo[1] == 1062) {
                Log::error("Duplicate entry error while creating inventory: " . $e->getMessage(), ['exception' => $e]);
                return response()->json(['error' => 'Duplicate entry: Inventory Code with the same data already exists.'], 400);
            }

            // Log the database error with more context
            Log::error("Failed to save new inventory item. Database error: {$e->getMessage()}", ['exception' => $e]);

            // Return a database error response
            return response()->json(['error' => 'Database error while saving new inventory'], 500);
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollback();
            // Log the error for debugging purposes
            Log::error("Error while creating inventory: " . $e->getMessage(), ['exception' => $e]);
            // Handle exceptions and return an error response
            return response()->json([
                "error" => "An error occurred while creating the inventory: " . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
