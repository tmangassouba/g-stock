<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'id'              => $this->id,
            'designation'     => $this->designation,
            'code'            => $this->code,
            'ref_fabricant'   => $this->ref_fabricant,
            'description'     => $this->description,
            'stock_min'       => $this->stock_min,
            'stock_ouverture' => $this->stock_ouverture,
            'stock_max'       => $this->stock_max,
            'unite_id'        => $this->unite_id,
            'unite'           => $this->unite,
            'prix'            => $this->prix,
            'quantite'        => $this->quantite,
            'image'           => $this->image,
            'image_url'       => $this->image ? Storage::url('products/'.$this->image) : Storage::url('products/default.png'),
            'stock'           => $this->stock(),
            // 'files'           => FileResource::collection($this->files),
            'created_at'      => $this->created_at ? $this->created_at->format('d/m/Y H:i') : null,
            'updated_at'      => $this->updated_at ? $this->updated_at->format('d/m/Y H:i') : null,
        ];
    }
}
