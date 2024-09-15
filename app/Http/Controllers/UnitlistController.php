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
use App\Models\Unitlist;
use App\Http\Resources\UnitlistCollection;
use Illuminate\Database\QueryException;

class UnitlistController extends Controller
{
    /**
     * Fetch a list of suppliers for a dropdown.
     */
    public function dropdownlist(Request $req)
    {
        try {
            // Query the records and get the unit list
            $unitlist = Unitlist::select('unit')->distinct()->orderBy('unit')->get();

            // Return the unit list
            return new UnitlistCollection($unitlist);
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
