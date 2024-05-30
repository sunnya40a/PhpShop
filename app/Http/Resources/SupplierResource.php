<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            's_name' => $this->s_name,
            'mobile1' => $this->mobile1,
            'mobile2' => $this->mobile2,
            'c_person' => $this->c_person,
            'contact_info' => $this->contact_info,
        ];
    }
}

// class supplierResource extends JsonResource
// {
//     /**
//      * Transform the resource into an array.
//      *
//      * @return array<string, mixed>
//      */
//     public function toArray(Request $request): array
//     {
//         return parent::toArray($request);
//     }
// }
