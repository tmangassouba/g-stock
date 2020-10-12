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
            'designation' => 'required|unique:products,designation'.($this->product ? ','.$this->product->id : ''),
            'stock_min'   => 'required|integer|min:1',
            'stock_max'   => 'nullable|integer|min:1',
            'prix'        => 'nullable|integer|min:1',
            'unite_id'    => 'required',
            'quantite'    => 'required|integer|min:1'
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Veuillez renseigner ce champ.',
            'unique' => 'Ce produit existe déjà.',
            'integer' => 'Veuillez renseigner une valeur incorrecte',
            'min' => 'Valeur incorrecte',
        ];
    }
}
