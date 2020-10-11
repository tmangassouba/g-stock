<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
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
            'designation' => 'required',
            'stock_min' => 'required|integer|min:0',
            'stock_max' => 'required|integer|min:0',
            // 'unite_id' => ['required',
            'quantite' => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Ce champ est obligatoire',
            'integer' => 'Valeur incorrecte',
            'min' => 'Valeur incorrecte',
        ];
    }
}
