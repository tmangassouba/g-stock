<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FileResource extends JsonResource
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
            'name'          => $this->name,
            'file_name'     => $this->file_name,
            'extension'     => $this->extension,
            'filable_id'    => $this->filable_id,
            'filable_type'  => $this->filable_type,
            'file_url'      => $this->file_name ? Storage::url('files/'.$this->file_name) : null,
            'created_at'    => $this->created_at ? $this->created_at->format('d/m/Y H:i') : null,
            'updated_at'    => $this->updated_at ? $this->updated_at->format('d/m/Y H:i') : null,
        ];
    }
}
