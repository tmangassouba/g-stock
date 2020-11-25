<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
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
            'id'          => $this->id,
            'name'        => $this->name,
            'first_name'  => $this->first_name,
            'last_name'   => $this->last_name,
            'actif'       => $this->actif,
            'email'       => $this->email,
            'phone'       => $this->phone,
            'roles'       => $this->roles,
            'roles_id'    => $this->roles_id(),
            'moi'         => $request->user() && $this->id == $request->user()->id,
            'avatar'      => $this->profile_photo_path ? Storage::url($this->profile_photo_path) : Storage::url('profile-photos/user-profile.png'),
            // 'shop_id'     => $this->shop_id,
            // 'shop'        => new Shop($this->shop),
            'created_at'  => $this->created_at ? $this->created_at->format('d/m/Y H:i') : null,
            'updated_at'  => $this->updated_at ? $this->updated_at->format('d/m/Y H:i') : null,
        ];
    }
}
