<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Http\Requests\StoreSalesHistories;
use App\Http\Requests\UpdateSalesHistories;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\SalesHistory;
use App\Models\Inventory;
use App\Http\Resources\SalesHistoriesResource;
use App\Http\Resources\SalesHistoriesCollection;
use Illuminate\Database\QueryException;


class SalesHistoryController extends Controller
{
    //below function is for routing handler for 
    // Route::get('/sales/list', [SalesHistoryController::class, 'index']);
    // Route::get('/sales/list/{invoice}', [SalesHistoryController::class, 'show']);
    public function handleSales(Request $request)
    {
        // Retrieve the PO parameter from the query parameters in the request
        $invoice = $request->query('invoice');

        if ($invoice) {
            // If PO parameter is present, show the purchase with that PO
            return $this->show($request);
        } else {
            // If PO parameter is not present, list all purchases
            return $this->index($request);
        }
    }
    // to generate new Invoice Number.
    public function generateInvoiceNumber()
    {
        $invoiceNumber = (int) $this->invoices()->max('invoice_number') + 1; // Assuming you have an invoices table
        return str_pad($invoiceNumber, 7, '0', STR_PAD_LEFT); // Pad with leading zeros to make it 7 digits
    }

    //============================================================================================//
    // API endpoint for showing a list of categories
    public function index(Request $req)
    {
        // Retrieve request parameters with default values
        $page = (int) $req->query('page', 1); // Default page is 1
        $limit = (int) $req->query('limit', 10); // Default limit is 10
        $sortBy = $req->query('sortBy', 'InvoiceNO'); // Default sort by 'category_code'
        $sortOrder = $req->query('sortOrder', 'asc'); // Default sort order is 'asc'
        $searchTerm = $req->query('search', ''); // Default search term is an empty string
        $datef = $req->query('datef', ''); // Default datef is an empty string
        $datee = $req->query('datee', ''); // Default datee is an empty string

        // Validate request parameters
        $validator = Validator::make($req->all(), [
            'page' => 'integer|min:1',
            'limit' => 'integer|min:1|max:100',
            'sortBy' => 'string|in:InvoiceNO,Sdate,item_list,description,category,price,user,qty',
            'sortOrder' => 'string|in:asc,desc',
            'search' => 'string|max:255|nullable',
            'datef' => 'date|nullable',
            'datee' => 'date|nullable|after_or_equal:datef',
        ]);

        if ($validator->fails()) {
            // Return validation errors if any
            return response()->json($validator->errors(), 422);
        }

        // Query the records with optional filters
        $query = SalesHistory::query();

        // Apply date range filter if both datef and datee are provided
        if ($datef && $datee) {
            $query->whereBetween('Sdate', [$datef, $datee]);
        }

        //sanitize searchText.

        $searchTerm = $this->sanitizeSearchText($searchTerm);

        // Apply search filter if a search term is provided
        if ($searchTerm !== '') {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('InvoiceNO', 'like', '%' . $searchTerm . '%')
                    ->orWhere('Sdate', 'like', '%' . $searchTerm . '%')
                    ->orWhere('item_list', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%')
                    ->orWhere('qty', 'like', '%' . $searchTerm . '%')
                    ->orWhere('unit', 'like', '%' . $searchTerm . '%')
                    ->orWhere('price', 'like', '%' . $searchTerm . '%')
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

        // Retrieve the records
        $categories = $query->get();

        // Return the categories as a collection with total count
        return new SalesHistoriesCollection($categories, $totalCount);
    } // End of API endpoint for showing sales history list

    //============================================================================================//

    // API endpoint for showing a specific invoices
    public function show(Request $req)
    {
        // Retrieve the code query parameter from the request
        $invoice = $req->query('invoice');

        // Validate the code parameter
        if (!$invoice) {
            // Return a 400 Bad Request response if the code parameter is missing
            return response()->json([
                "error" => "The invoice parameter is required"
            ], 400);
        }

        // Find the sales with the specified invoice code.
        $sales = SalesHistory::where('InvoiceNO', $invoice)->first();
        // Check if the category exists
        if (!$sales) {
            // Return a 404 Not Found response if no category is found
            return response()->json(
                [
                    "error" => "No category found for code: {$invoice}"
                ],
                404
            );
        }
        // Return the category as a resource
        return new SalesHistoriesResource($sales);
    } // End of API endpoint for showing a specific category















    function sanitizeSearchText(string $searchText): string
    {
        // Remove unwanted characters and replace multiple spaces with single space
        $sanitizedText = preg_replace('/[^\s\w\-()!,.@[\]]/', '', $searchText);
        $sanitizedText = preg_replace('/\s+/', ' ', $sanitizedText);

        return $sanitizedText;
    }
}
