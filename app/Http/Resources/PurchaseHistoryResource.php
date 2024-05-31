<?php
//App\Http\Resources\PurchaseHistoryResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        // Check if u_price and p_price exist in $data and convert them to float if they exist
        if (array_key_exists('u_price', $data)) {
            $data['u_price'] = floatval($data['u_price']);
        }
        if (array_key_exists('p_price', $data)) {
            $data['p_price'] = floatval($data['p_price']);
        }
        // Calculate the value for NewField based on other data in $data
        //$data['NewField'] = $this->calculateNewField($data); // Replace with your logic
        //return $data;
        return $data; // Use collection's toArray directly

    }


    // Add a separate method for calculation (optional)
    private function calculateNewField(array $data): string
    {
        // Implement logic to calculate the value based on $data
        // This could involve calculations or manipulations
        return 'Calculated value';
    }
}
