<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Http\Requests\StorePurchaseHistory;
use App\Http\Requests\UpdatePurchaseHistory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\PurchaseHistory;
use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\PaymentStatus;
use App\Http\Resources\PurchaseHistoryResource;
use App\Http\Resources\PurchaseHistoryCollection;
use Illuminate\Database\QueryException;


class PurchaseHistoryController extends Controller
{

    //below function is for routing handler for 
    // Route::get('/purchase/list', [PurchaseHistoryController::class, 'ListPurchase']);
    // Route::get('/purchase/list/{PO}', [PurchaseHistoryController::class, 'ShowPurchase']);
    public function handlePurchase(Request $request)
    {
        // Retrieve the PO parameter from the query parameters in the request
        $PO = $request->query('PO');

        if ($PO) {
            // If PO parameter is present, show the purchase with that PO
            return $this->ShowPurchase($request);
        } else {
            // If PO parameter is not present, list all purchases
            return $this->ListPurchase($request);
        }
    }

    //============================================================================================//

    // Api end point for Purchase List
    public function ListPurchase(Request $req)
    {
        // Retrieve request parameters with validation
        $validatedData = $req->validate([
            'page' => 'integer|min:1',
            'limit' => 'integer|min:1|max:100',
            'sortBy' => 'string|in:PO,Pdate,item_list,material_desc,category,p_price,user,qty',
            'sortOrder' => 'string|in:asc,desc',
            'search' => 'string|max:255|nullable',
            'datef' => 'date|nullable',
            'datee' => 'date|nullable|after_or_equal:datef',
        ]);

        $page = $validatedData['page'] ?? 1; // Default page is 1
        $limit = $validatedData['limit'] ?? 10; // Default limit is 10
        $sortBy = $validatedData['sortBy'] ?? 'PO'; // Default sort by 'PO'
        $sortOrder = $validatedData['sortOrder'] ?? 'asc'; // Default sort order is 'asc'
        $searchTerm = $validatedData['search'] ?? ''; // Default search term is an empty string
        $datef = $validatedData['datef'] ?? ''; // Default datef is an empty string
        $datee = $validatedData['datee'] ?? ''; // Default datee is an empty string

        // Query the records with optional filters
        $query = PurchaseHistory::query();

        // Apply date range filter if both datef and datee are provided
        if ($datef && $datee) {
            $query->whereBetween('Pdate', [$datef, $datee]);
        }

        //sanitize searchText.

        $searchTerm = $this->sanitizeSearchText($searchTerm);

        // Apply search filter if a search term is provided
        if ($searchTerm !== '') {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('PO', 'like', '%' . $searchTerm . '%')
                    ->orWhere('Pdate', 'like', '%' . $searchTerm . '%')
                    ->orWhere('item_list', 'like', '%' . $searchTerm . '%')
                    ->orWhere('material_desc', 'like', '%' . $searchTerm . '%')
                    ->orWhere('category', 'like', '%' . $searchTerm . '%')
                    ->orWhere('u_price', 'like', '%' . $searchTerm . '%')
                    ->orWhere('p_price', 'like', '%' . $searchTerm . '%')
                    ->orWhere('qty', 'like', '%' . $searchTerm . '%')
                    ->orWhere('unit', 'like', '%' . $searchTerm . '%')
                    ->orWhere('user', 'like', '%' . $searchTerm . '%');
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

    //============================================================================================//

    // Api end point for Show Particular Purchase list
    public function ShowPurchase(Request $req)
    {
        // Retrieve the code query parameter from the request
        $PO = $req->query('PO');

        // Validate the code parameter
        if (!$PO) {
            // Return a 400 Bad Request response if the code parameter is missing
            return response()->json([
                "error" => "The code parameter is required"
            ], 400);
        }

        // Find the purchase history record with the specified PO
        $purchaseHistory = PurchaseHistory::where('PO', $PO)->first();

        // Check if the purchase history record exists
        if (!$purchaseHistory) {
            // Return an error response with requested PO value
            return response()->json([
                "message" => "Purchase history record not found for PO: {$PO}"
            ], 404);
        }

        // Return the purchase history record using the resource
        return new PurchaseHistoryResource($purchaseHistory);
    }
    // End of Api end point for Show Particular Purchase list

    //============================================================================================//

    // Api end point for Delete Purchase
    public function DelPurchase(Request $req)
    {
        // Retrieve the PO query parameter from the request
        $PO = $req->query('PO');

        // Validate that the PO parameter is present
        if (!$PO) {
            return response()->json(
                ["message" => "PO query parameter is required"],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Begin a transaction
        DB::beginTransaction();

        try {
            // Retrieve the purchase history record with the specified PO
            $purchaseHistory = PurchaseHistory::where('PO', $PO)->firstOrFail();

            // Retrieve the item list and quantity from the purchase history record
            $itemList = $purchaseHistory->item_list;
            $qty = $purchaseHistory->qty;

            // Find the corresponding inventory record
            $inventory = Inventory::where('Item_list', $itemList)->first();

            if ($inventory) {
                // Check if deducting the quantity would result in negative stock
                $newInventoryQty = $inventory->qty - $qty;
                if ($newInventoryQty < 0) {
                    Log::error("Deleting quantity from $inventory->qty to $qty would create negative stock.");
                    throw new \Exception("Whoops! Reducing the stock from $inventory->qty to $qty would create negative stock.");
                }

                $inventory->qty -= $qty;
                $inventory->save(); // Save the updated inventory record
            }

            // Delete the purchase history record
            $purchaseHistory->delete();

            // Commit the transaction if everything is successful
            DB::commit();

            return response()->json(["message" => "Purchase with PO {$PO} deleted successfully"], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::error("Purchase with PO {$PO} not found: " . $e->getMessage());
            return response()->json(["message" => "Purchase with PO {$PO} not found"], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting purchase: " . $e->getMessage());
            return response()->json(["message" => "An error occurred while deleting the purchase"], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // End of Api end point for Delete Purchase

    //============================================================================================//

    // Api end point for Save Purchase Entry
    public function SavePurchase(StorePurchaseHistory $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), $request->rules());
        //$validator->after(['Pdate' => 'date']); // Assuming a custom validation rule for date format
        // $validator->validate([
        //     'Pdate' => 'date',
        //     // Other validation rules...
        // ]);


        if ($validator->fails()) {
            // Return specific validation errors with user-friendly messages
            return response()->json($validator->errors()->messages(), 422);
        }

        $twoDaysAgo = strtotime('-2 days', time());
        $submittedDate = strtotime($request->input('Pdate'));

        if ($submittedDate < $twoDaysAgo) {
            return response()->json([
                "error" => "You are not allowed to enter dates before 3 days."
            ], 422);
        }


        $currentUser = Auth::user();

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create a new PurchaseHistory instance
            $purchaseHistory = new PurchaseHistory();

            // Assign validated data using a more descriptive approach
            $purchaseHistory->fill([
                'PO' => $request->has('PO') && $request->PO == 1 ? $this->generatePO($request->Pdate) : $request->PO,
                'Pdate' => $request->Pdate,
                'item_list' => $request->item_list,
                'material_desc' => $request->material_desc,
                'qty' => $request->qty,
                'unit' => $request->unit,
                'u_price' => $request->u_price,
                'p_price' => $request->p_price,
                'user' => $currentUser->name,
                'category' => $request->category,
                'supplier_id' => $request->supplier_id,
                'Rdate' => $request->Rdate,
                'paid_status' => $request->paid_status,
            ]);

            // Save the new purchase history record
            $purchaseHistory->save();

            // Update or create the item in the inventory table
            $updatedInventoryItem = Inventory::updateOrCreate(
                ['item_list' => $request->item_list], // Condition to find the item
                [
                    'material_desc' => $request->material_desc,
                    'qty' => DB::raw("COALESCE(qty, 0) + {$request->qty}"), // Update the quantity
                    'category' => $request->category,
                ]
            );

            // Commit the transaction if everything is successful
            DB::commit();

            // Retrieve the saved record with fresh data and only relevant fields
            $savedRecord = $purchaseHistory->fresh()->only([
                'PO',
                'Pdate',
                'item_list',
                'material_desc',
                'qty',
                'unit',
                'u_price',
                'p_price',
                'user',
                'category',
                'supplier_id',
                'Rdate',
                'paid_status',
            ]);

            // Return a success response with a more informative message
            return response()->json([
                "message" => "Purchase data saved successfully.",
                "data" => $savedRecord,
            ], 201);
        } catch (QueryException $e) {
            // Roll back the transaction in case of a database error
            DB::rollback();

            // Log the database error with more context
            Log::error("Failed to save purchase data. Database error: {$e->getMessage()}");

            // Return a database error response
            return response()->json(['error' => 'Database error while saving purchase data'], 500);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollback();

            // Log the error with more context and return a specific error message
            Log::error("Error saving purchase data: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving purchase data'], 500);
        }
    }  // End of Api end point for Save Purchase Entry

    //============================================================================================//

    //Api end point for Update Purchase Entry
    public function UpdatePurchase(UpdatePurchaseHistory $request)
    {
        $PO = $request->query('PO');

        // Validate that the PO parameter is present
        if (!$PO) {
            return response()->json(
                ["message" => "PO query parameter is required"],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Validate the request data including PO uniqueness and date format
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
                    // Log the error with request data
                    Log::error("Updating quantity from $originalQty to $updatedQty would create negative stock. Request data: " . json_encode($request->all()));

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
                    'material_desc' => $request->material_desc,
                    'qty' => $updatedQty,
                    'unit' => $request->unit,
                    'category' => $request->category,
                ]);
            }

            if ($request->has('Pdate') && $request->PO != $purchaseHistory->PO) {
                return response()->json([
                    "error" => "You are not allowed to change the PO number."
                ], 422);
            }

            if ($request->has('Pdate') && $request->Pdate != $purchaseHistory->Pdate) {
                return response()->json([
                    "error" => "You are not allowed to change the purchase date (Pdate)."
                ], 422);
            }

            // Update the purchase history record
            $purchaseHistory->update([
                //'PO' => $request->PO,  // I don't want client can change PO number as PO number will be same even though user sent new PO number
                //'PO' => $PO,
                //'Pdate' => $purchaseHistory->Pdate, // I don't want them to change purchased date too.
                'item_list' => $request->item_list,
                'material_desc' => $request->material_desc,
                'qty' => $request->qty,
                'unit' => $request->unit,
                'u_price' => $request->u_price,
                'p_price' => $request->p_price,
                'user' => $currentUser->name, // User system put automatically.
                'category' => $request->category,
                'supplier_id' => $request->supplier_id,
                'Rdate' => $request->Rdate,
                'paid_status' => $request->paid_status,
            ]);

            // Commit the transaction if everything is successful
            DB::commit();

            // Retrieve the updated record with fresh data
            $updatedRecord = $purchaseHistory->fresh()->only([
                'PO',
                'Pdate',
                'item_list',
                'material_desc',
                'qty',
                'unit',
                'u_price',
                'p_price',
                'user',
                'category',
                'supplier_id',
                'Rdate',
                'paid_status',
            ]);

            // Return a success response with a more informative message
            return response()->json([
                "message" => "Purchase with PO {$PO} updated successfully.",
                "data" => $updatedRecord,
            ], 200);
        } catch (ModelNotFoundException $exception) {
            // If record is not found, roll back the transaction
            DB::rollback();
            return response()->json([
                "message" => "Record not found with PO: {$request->PO}",
            ], 404);
        } catch (QueryException $e) {
            // Catch specific database exceptions for better error handling
            DB::rollback();
            Log::error("Database error while updating purchase: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating purchase data'], 500);
        } catch (\Exception $e) {
            // Rollback on unexpected exceptions
            DB::rollback();
            // Log the error and return a failure response
            Log::error($e->getMessage());
            return response()->json(['error' => 'Failed to update purchase data'], 500);
        }
    }
    // End of Api end point for Update Purchase Entry

    //New multicolumn function to list multipal column from multiple table
    public function detailelist()
    {
        // $detailedPurchases = PurchaseHistory::leftJoin('suppliers', 'purchaseHistory.supplier_id', '=', 'suppliers.id')
        //     ->leftJoin('paymentstatuses', 'purchaseHistory.paid_status', '=', 'paymentstatuses.id')
        //     ->select('purchaseHistory.PO', 'purchaseHistory.Pdate', 'suppliers.s_name', 'paymentstatuses.status AS payment_status')
        //     ->orderBy('purchaseHistory.PO')
        //     ->get();

        // return response()->json($detailedPurchases);

        $detailedPurchases = PurchaseHistory::leftJoin('suppliers', 'purchaseHistory.supplier_id', '=', 'suppliers.id')
            ->leftJoin('paymentstatuses', 'purchaseHistory.paid_status', '=', 'paymentstatuses.id')
            ->select('purchaseHistory.PO', 'purchaseHistory.Pdate', 'suppliers.s_name', 'paymentstatuses.status AS payment_status')
            ->orderBy('purchaseHistory.PO')
            ->get();

        // Get total count of records
        $totalCount = $detailedPurchases->count();

        // Return as a collection using PurchaseHistoryCollection
        return new PurchaseHistoryCollection(PurchaseHistoryResource::collection($detailedPurchases), $totalCount);
    }


    //============================================================================================//

    public function LastPO(Request $req)
    {
        $Pdate = $req->query('Pdate');

        // Validate the request data
        $validator = Validator::make(
            $req->all(),
            [
                'Pdate' => 'required|date', // Pdate is required and should follow the Y-m-d format
            ]
        );

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422); // Return validation errors with a 422 status code
        }

        // Initialize TempPO
        $TempPO = 0;

        // Retrieve the largest PO number for the given Pdate
        $largestPO = PurchaseHistory::whereDate('Pdate', $Pdate)
            ->max('PO');

        if ($largestPO !== null) {
            // Increment the largest PO by 1 and set it as the new PO
            $TempPO = (int)$largestPO + 1;
        } else {
            // Format the given date as ymd and concatenate with '0001'
            $datePart = Carbon::parse($Pdate)->format('ymd'); // Convert and format the date
            $TempPO = (int)($datePart . '0001');
        }

        // Return a JSON response with the new PO number
        return response()->json(["newPO" => $TempPO], Response::HTTP_OK);
    } //End of LastPO function that send last PO number through api.

    //============================================================================================//

    //Supporting function that generate PO number.
    private function generatePO($Pdate)
    {
        // Initialize TempPO
        $TempPO = 0;

        // Retrieve the largest PO number for the given Pdate
        $largestPO = PurchaseHistory::whereDate('Pdate', $Pdate)
            ->max('PO');

        if ($largestPO !== null) {
            // Increment the largest PO by 1 and set it as the new PO
            $TempPO = (int)$largestPO + 1;
        } else {
            // Format the given date as ymd and concatenate with '0001'
            $datePart = Carbon::parse($Pdate)->format('ymd'); // Convert and format the date
            $TempPO = (int)($datePart . '0001');
        }

        // Return a JSON response with the new PO number
        return $TempPO;
    }

    function sanitizeSearchText(string $searchText): string
    {
        // Remove unwanted characters and replace multiple spaces with single space
        $sanitizedText = preg_replace('/[^\s\w\-()!,.@[\]]/', '', $searchText);
        $sanitizedText = preg_replace('/\s+/', ' ', $sanitizedText);

        return $sanitizedText;
    }
}    //End of generatePO function that generate PO number.
