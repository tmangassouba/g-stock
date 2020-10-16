<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|max:255',
            'last_name'  => 'required|max:255',
            'email'      => 'required|max:255|email|unique:users,email'.($this->user ? ','.$this->user->id : ''),
            'roles'      => 'required',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Veuillez renseigner ce champ.',
            'max'      => 'Trop de caractères',
            'email'    => 'Email incorrect',
            'unique'    => 'Ce mail est déjà utilisé.',
        ];
    }
}
