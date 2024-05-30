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
use App\Models\Suppliers;
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
            $suppliers = Suppliers::select('id', 's_name')->distinct()->orderBy('id')->get();

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

    /**
     * Fetch a detailed list of suppliers.
     */
    public function supplierlist(Request $req)
    {
        try {
            // Query the records and get the unique item_list and description values
            $suppliers = Suppliers::select('id', 's_name', 'mobile1', 'mobile2', 'c_person', 'contact_info')->distinct()->orderBy('id')->get();

            // Get the total record count
            $totalRecords = Suppliers::count();

            // Return the unique item_list and description values as JSON with totalRecords
            return new SupplierCollection($suppliers, $totalRecords);
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
}
