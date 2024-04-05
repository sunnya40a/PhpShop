<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseHistory;
use App\Http\Requests\UpdatePurchaseHistory;
use Illuminate\Http\Request;
use App\Models\PurchaseHistory;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PurchaseHistoryResource;
use App\Http\Resources\PurchaseHistoryCollection;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class PurchaseHistoryController extends Controller
{
    public function ListPurchase(Request $req)
    {
        return new PurchaseHistoryCollection(PurchaseHistory::paginate());
    }

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
    }


    public function SavePurchase(StorePurchaseHistory $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), $request->rules());
        if ($validator->fails()) {
            // Return validation errors if any
            return response()->json($validator->errors(), 422);
        }
        $currentUser = Auth::user();
        // Create a new PurchaseHistory instance
        $purchaseHistory = new PurchaseHistory();

        // Assign validated data to the PurchaseHistory object
        $purchaseHistory->PO = $request->PO;
        $purchaseHistory->Pdate = $request->Pdate;
        $purchaseHistory->item_list = $request->item_list;
        $purchaseHistory->description = $request->description;
        $purchaseHistory->qty = $request->qty;
        $purchaseHistory->price = $request->price;
        //$purchaseHistory->user = $request->user;
        $purchaseHistory->user = $currentUser->name;
        $purchaseHistory->category = $request->category;

        // Save the new purchase history record
        $purchaseHistory->save();

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
    }

    public function UpdatePurchase(UpdatePurchaseHistory $request, $PO)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), $request->rules());

        if ($validator->fails()) {
            // Return validation errors if any
            return response()->json($validator->errors(), 422);
        }

        try {
            // Find the record by PO
            $purchaseHistory = PurchaseHistory::where('PO', $PO)->firstOrFail();
            $currentUser = Auth::user();
            // Update the record with the validated data
            $purchaseHistory->update([
                'PO' => $request->PO,
                'Pdate' => $request->Pdate,
                'item_list' => $request->item_list,
                'description' => $request->description,
                'qty' => $request->qty,
                'price' => $request->price,
                //'user' => $request->user,
                'user' => $currentUser->name,
                'category' => $request->category,
            ]);

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
            // Return an error response if record not found
            return response()->json([
                "message" => "Record not found with PO: {$request->PO}",
            ], 404);
        }
    }
}
