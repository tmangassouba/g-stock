<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
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
            'statut'       => 'required',
            'customer_id'  => 'required',
            'date'         => 'nullable|date',
            'acompte'      => 'nullable|integer|min:0'
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Veuillez renseigner ce champ.',
            'datetime' => 'Date incorrecte',
            'integer'  => 'Valeur incorrecte',
            'min'      => 'Valeur incorrecte'
        ];
    }
}
