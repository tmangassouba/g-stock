<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'designation'   => $this->designation,
            'code'          => $this->code,
            'ref_fabricant' => $this->ref_fabricant,
            'description'   => $this->description,
            'stock_min'     => $this->stock_min,
            'stock_max'     => $this->stock_max,
            'unite_id'      => $this->unite_id,
            'unite'         => $this->unite,
            'prix'          => $this->prix,
            'quantite'      => $this->quantite,
            'stock'         => 0,
            'created_at'    => $this->created_at ? $this->created_at->format('d/m/Y') : null,
            'updated_at'    => $this->updated_at ? $this->updated_at->format('d/m/Y') : null,
        ];
    }
}
