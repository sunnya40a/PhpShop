<?php
//App\Http\Resources\PurchaseHistoryCollection.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\PurchaseHistoryResource;
use App\Models\PurchaseHistory;

class PurchaseHistoryCollection extends ResourceCollection
{

    // Property to hold the total record count
    protected $totalCount;

    /**
     * Constructor to initialize the resource collection and total count.
     *
     * @param mixed $resource
     * @param int $totalCount
     */
    public function __construct($resource, int $totalCount)
    {
        // Call the parent constructor
        parent::__construct($resource);

        // Set the total count to a class property
        $this->totalCount = $totalCount;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'data' => $this->collection,
        ];

        if ($this->totalCount !== null) {
            $data['TotalRecords'] = $this->totalCount;
        }

        return $data;
    }
}
