<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MagazinRequest extends FormRequest
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
            'name'    => 'required|max:255|unique:magazins,name'.($this->magazin ? ','.$this->magazin->id : ''),
            'adresse' => 'nullable|max:255',
            'ville'   => 'nullable|max:255',
            'phone'   => 'nullable|max:255',
            'email'   => 'nullable|max:255|email',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Veuillez renseigner ce champ.',
            'max'      => 'Trop de caractères.',
            'email'    => 'Email incorrect.',
            'unique'    => 'Ce magazin existe déjà.',
        ];
    }
}
