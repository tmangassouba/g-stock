<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'id'         => $this->id,
            'type'       => $this->type,
            'code'       => $this->code,
            'title'      => $this->title,
            'name'       => $this->name,
            'fisrt_name' => $this->fisrt_name,
            'last_name'  => $this->last_name,
            'address'    => $this->address,
            'city'       => $this->city,
            'telephone'  => $this->telephone,
            // 'mobile'     => $this->mobile,
            'email'      => $this->email,
            'website'    => $this->website,
            'company'    => $this->company,
            'created_at' => $this->created_at ? $this->created_at->format('d/m/Y H:i') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d/m/Y H:i') : null,
        ];
    }
}
