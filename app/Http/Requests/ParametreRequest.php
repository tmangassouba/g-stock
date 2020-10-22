<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParametreRequest extends FormRequest
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
            'name'      => 'required|max:255',
            'adresse_1' => 'nullable|max:255',
            'adresse_2' => 'nullable|max:255',
            'ville'     => 'nullable|max:255',
            'pays'      => 'nullable|max:255',
            'phone'     => 'nullable|max:255',
            'faxe'      => 'nullable|max:255',
            'site'      => 'nullable|max:255|url',
            'email'     => 'nullable|max:255|email',
        ];
    }
}
