<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'reference'     => $this->reference,
            'statut'        => $this->statut,
            'date'          => $this->date,
            'formated_date' => $this->date ? date('d M Y', strtotime($this->date)) : null,
            'customer_id'   => $this->customer_id,
            'description'   => $this->description,
            'customer'      => $this->customer,
            'created_at'    => $this->created_at ? $this->created_at->format('d/m/Y H:i') : null,
            'updated_at'    => $this->updated_at ? $this->updated_at->format('d/m/Y H:i') : null,
        ];
    }
}
