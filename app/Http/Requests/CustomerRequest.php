<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'type'        => 'required|max:100',
            'title'       => 'required|max:100',
            'fisrt_name'  => 'required|max:100',
            'last_name'   => 'required|max:100',
            'address'     => 'nullable|max:100',
            'city'        => 'nullable|max:100',
            'telephone'   => 'nullable|max:100',
            // 'mobile'      => 'nullable|max:100',
            'email'       => 'nullable|max:100',
            'website'     => 'nullable|max:100',
            'company'     => 'nullable|max:100'
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Veuillez renseigner ce champ.',
            'max' => 'Trop de caractères.'
        ];
    }
}
