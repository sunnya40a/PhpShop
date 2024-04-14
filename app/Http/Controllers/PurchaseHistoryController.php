<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UpdatePurchaseHistory;
use Illuminate\Http\Request;
use App\Models\PurchaseHistory;
use App\Models\Inventory;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PurchaseHistoryResource;
use App\Http\Resources\PurchaseHistoryCollection;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class PurchaseHistoryController extends Controller
{


    // Api end point for Purchase List
    public function ListPurchase(Request $req)
    {
        // Retrieve request parameters with default values
        $page = (int) $req->query('page', 1); // Default page is 1
        $limit = (int) $req->query('limit', 10); // Default limit is 10
        $sortBy = $req->query('sortBy', 'PO'); // Default sort by 'PO'
        $sortOrder = $req->query('sortOrder', 'asc'); // Default sort order is 'asc'
        $searchTerm = $req->query('search', ''); // Default search term is an empty string
        $datef = $req->query('datef', ''); // Default datef is an empty string
        $datee = $req->query('datee', ''); // Default datee is an empty string

        // Query the records with optional filters
        $query = PurchaseHistory::query();

        // Apply date range filter if both datef and datee are provided
        if ($datef !== '' && $datee !== '') {
            $query->whereBetween('Pdate', [$datef, $datee]);
        }

        // Apply search filter if a search term is provided
        if ($searchTerm !== '') {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('PO', 'like', '%' . $searchTerm . '%')
                    ->orWhere('Pdate', 'like', '%' . $searchTerm . '%')
                    ->orWhere('item_list', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%')
                    ->orWhere('category', 'like', '%' . $searchTerm . '%')
                    ->orWhere('Price', 'like', '%' . $searchTerm . '%')
                    ->orWhere('User', 'like', '%' . $searchTerm . '%');
            });
        }

        // Get the total record count before applying pagination
        $totalCount = $query->count();

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        // Apply pagination
        $query->skip(($page - 1) * $limit)
            ->take($limit);

        // Get the records
        $purchaseHistories = $query->get();
        return new PurchaseHistoryCollection($purchaseHistories, $totalCount);
    }
    //// End for Api end point for Purchase List

    //Api end point for Show Particular Purchase list
    public function ShowPurchase(Request $req, PurchaseHistory $PO)
    {
        return new PurchaseHistoryResource($PO);
    }

    public function DelPurchase(Request $req, $PO)
    {
        $deletedRecords = PurchaseHistory::where('PO', $PO)->delete();

        if ($deletedRecords > 0) {
            return response()->json(["message" => "Purchase with PO {$PO} successfully deleted"], Response::HTTP_OK);
        } else {
            return response()->json(["message" => "Purchase with PO {$PO} not found"], Response::HTTP_NOT_FOUND);
        }
    } // End of Api end point for Show Particular Purchase list

    // Api end point for Save Purchase Entry
    public function SavePurchase(StorePurchaseHistory $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), $request->rules());
        if ($validator->fails()) {
            // Return validation errors if any
            return response()->json($validator->errors(), 422);
        }

        $currentUser = Auth::user();

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create a new PurchaseHistory instance
            $purchaseHistory = new PurchaseHistory();

            // Assign validated data to the PurchaseHistory object
            $purchaseHistory->PO = $request->PO;
            $purchaseHistory->Pdate = $request->Pdate;
            $purchaseHistory->item_list = $request->item_list;
            $purchaseHistory->description = $request->description;
            $purchaseHistory->qty = $request->qty;
            $purchaseHistory->price = $request->price;
            $purchaseHistory->user = $currentUser->name;
            $purchaseHistory->category = $request->category;

            // Save the new purchase history record
            $purchaseHistory->save();

            // Use updateOrCreate to find the item in the inventory table by Item_list and create or update the record
            $inventory = Inventory::updateOrCreate(
                ['item_list' => $request->item_list], // Condition to find the item
                [
                    'description' => $request->description,
                    'qty' => DB::raw("COALESCE(qty, 0) + {$request->qty}"), // Update the quantity
                    'category' => $request->category,
                ]
            );
            $inventory->save();

            // Commit the transaction if everything is successful
            DB::commit();

            // Retrieve the saved record with fresh data
            $savedRecord = $purchaseHistory->fresh()->only([
                'PO',
                'Pdate',
                'item_list',
                'qty',
                'price',
                'description',
                'category',
                'user',
            ]);

            // Return a success response
            return response()->json([
                "message" => "Data submitted successfully.",
                "data" => $savedRecord,
            ], 201);
        } catch (\Exception $e) {
            // If there is an error, roll back the transaction
            DB::rollback();

            // Log the error and return a failure response
            Log::error($e->getMessage());
            return response()->json(['error' => 'Failed to save purchase data'], 500);
        }
    }  // End of Api end point for Save Purchase Entry

    //Api end point for Update Purchase Entry
    public function UpdatePurchase(UpdatePurchaseHistory $request, $PO)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), $request->rules());

        if ($validator->fails()) {
            // Return validation errors if any
            return response()->json($validator->errors(), 422);
        }

        $currentUser = Auth::user();

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Find the record by PO
            $purchaseHistory = PurchaseHistory::where('PO', $PO)->firstOrFail();

            // Calculate the quantity difference
            $originalQty = $purchaseHistory->qty;
            $updatedQty = $request->qty;
            $quantityDifference = $updatedQty - $originalQty;

            // Find the inventory record for the item
            $inventory = Inventory::where('Item_list', $request->item_list)->first();

            // Check if inventory exists and calculate the new quantity
            if ($inventory) {
                $newInventoryQty = $inventory->qty + $quantityDifference;

                // Check if the new quantity would be less than 0
                if ($newInventoryQty < 0) {
                    // Log the error
                    Log::error("Updating quantity from $originalQty to $updatedQty would create negative stock.");

                    // Return an error response
                    return response()->json([
                        "error" => "Whoops! There seems to be a problem updating your inventory. Reducing the stock from $originalQty to $updatedQty would create negative stock."
                    ], 422);
                }

                // Adjust the inventory quantity
                $inventory->qty = $newInventoryQty;
                $inventory->save();
            } else {
                // If inventory doesn't exist, create a new record with the updated quantity
                // since quantityDifference in this case is the new quantity
                Inventory::create([
                    'Item_list' => $request->item_list,
                    'description' => $request->description,
                    'qty' => $updatedQty,
                    'category' => $request->category,
                ]);
            }

            // Update the purchase history record
            $purchaseHistory->update([
                'PO' => $request->PO,
                'Pdate' => $request->Pdate,
                'item_list' => $request->item_list,
                'description' => $request->description,
                'qty' => $updatedQty,
                'price' => $request->price,
                'user' => $currentUser->name,
                'category' => $request->category,
            ]);

            // Commit the transaction if everything is successful
            DB::commit();

            // Retrieve the updated record with fresh data
            $updatedRecord = $purchaseHistory->fresh()->only([
                'PO',
                'Pdate',
                'item_list',
                'qty',
                'price',
                'description',
                'category',
                'user',
            ]);

            // Return a success response
            return response()->json([
                "message" => "Data updated successfully.",
                "data" => $updatedRecord,
            ], 200);
        } catch (ModelNotFoundException $exception) {
            // If record is not found, roll back the transaction
            DB::rollback();
            return response()->json([
                "message" => "Record not found with PO: {$request->PO}",
            ], 404);
        } catch (\Exception $e) {
            // If there is an error, roll back the transaction
            DB::rollback();

            // Log the error and return a failure response
            Log::error($e->getMessage());
            return response()->json(['error' => 'Failed to update purchase data'], 500);
        }
    }
    // End of Api end point for Update Purchase Entry














    // public function UpdatePurchase(UpdatePurchaseHistory $request, $PO)
    // {
    //     // Validate the request data
    //     $validator = Validator::make($request->all(), $request->rules());

    //     if ($validator->fails()) {
    //         // Return validation errors if any
    //         return response()->json($validator->errors(), 422);
    //     }

    //     try {
    //         // Find the record by PO
    //         $purchaseHistory = PurchaseHistory::where('PO', $PO)->firstOrFail();
    //         $currentUser = Auth::user();
    //         // Update the record with the validated data
    //         $purchaseHistory->update([
    //             'PO' => $request->PO,
    //             'Pdate' => $request->Pdate,
    //             'item_list' => $request->item_list,
    //             'description' => $request->description,
    //             'qty' => $request->qty,
    //             'price' => $request->price,
    //             //'user' => $request->user,
    //             'user' => $currentUser->name,
    //             'category' => $request->category,
    //         ]);

    //         // Retrieve the updated record with fresh data
    //         $updatedRecord = $purchaseHistory->fresh()->only([
    //             'PO',
    //             'Pdate',
    //             'item_list',
    //             'qty',
    //             'price',
    //             'description',
    //             'category',
    //             'user',
    //         ]);

    //         // Return a success response
    //         return response()->json([
    //             "message" => "Data updated successfully.",
    //             "data" => $updatedRecord,
    //         ], 200);
    //     } catch (ModelNotFoundException $exception) {
    //         // Return an error response if record not found
    //         return response()->json([
    //             "message" => "Record not found with PO: {$request->PO}",
    //         ], 404);
    //     }
    // }
}
