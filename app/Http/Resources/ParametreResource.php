<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ParametreResource extends JsonResource
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
            'name'      => $this->name,
            'adresse_1' => $this->adresse_1,
            'adresse_2' => $this->adresse_2,
            'ville'     => $this->ville,
            'pays'      => $this->pays,
            'phone'     => $this->phone,
            'faxe'      => $this->faxe,
            'site'      => $this->site,
            'email'     => $this->email,
            'devise_id' => $this->devise_id,
            'devise'    => $this->devise_id ? $this->devise->symbole : '-',
            'image'     => $this->image,
            'image_url' => $this->image ? Storage::url($this->image) : null,
        ];
    }
}
