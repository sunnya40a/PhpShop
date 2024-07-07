<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SupplierCollection extends ResourceCollection
{
    protected $totalRecords;
    /**
     * Constructor to initialize the resource collection and total count.
     *
     * @param mixed $resource
     * @param int $totalCount
     */
    public function __construct($resource, $totalRecords = null)
    {
        // Call the parent constructor
        parent::__construct($resource);
        // Set the total count to a class property
        $this->totalRecords = $totalRecords;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<int|string, mixed>
     */
    public function toArray($request)
    {
        $data = [
            'data' => $this->collection,
        ];

        if ($this->totalRecords !== null) {
            $data['TotalRecords'] = $this->totalRecords;
        }

        return $data;
    }
}
