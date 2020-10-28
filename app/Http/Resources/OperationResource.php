<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OperationResource extends JsonResource
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
            'reference'       => $this->reference,
            'magazin_from_id' => $this->magazin_from_id,
            'magazin_to_id'   => $this->magazin_to_id,
            'description'     => nl2br(e($this->description)),
            'date'            => $this->date,
            'type'            => $this->type,
            'user_id'         => $this->user_id,
            'magazinFrom'     => new MagazinResource($this->magazinFrom),
            'magazinTo'       => new MagazinResource($this->magazinTo),
            'user'            => new UserResource($this->user),
            'products'        => ProductResource::collection($this->products),
            // 'products'        => $this->products,
            'created_at'      => $this->created_at ? $this->created_at->format('d/m/Y H:i') : null,
            'updated_at'      => $this->updated_at ? $this->updated_at->format('d/m/Y H:i') : null,
        ];
    }
}
