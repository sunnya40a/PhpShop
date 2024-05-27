<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Inventory;
use App\Http\Resources\InventoryResource;
use App\Http\Resources\InventoryCollection;
use Illuminate\Database\QueryException;

class InventoryController extends Controller
{
    public function itemlist(Request $req)
    {
        try {
            // Query the records and get the unique item_list and description values
            $itemscode = Inventory::select('item_list', 'description', 'unit', 'category')->distinct()->orderBy('item_list')->get();

            // Return the unique item_list and description values as JSON
            return response()->json([
                'data' => $itemscode
            ]);
        } catch (QueryException $e) {
            Log::error('Database query error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching the inventory items.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
