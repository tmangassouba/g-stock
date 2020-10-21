<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeviseRequest extends FormRequest
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
            'name'    => 'required|max:255|unique:devises,name'.($this->devise ? ','.$this->devise->id : ''),
            'symbole' => 'required|max:255|unique:devises,symbole'.($this->devise ? ','.$this->devise->id : '')
        ];
    }

    public function messages()
    {
        return [
            'required'       => 'Veuillez renseigner ce champ.',
            'max'            => 'Trop de caractères.',
            'name.unique'    => 'Ce nom existe déjà.',
            'symbole.unique' => 'Ce symbole existe déjà.',
        ];
    }
}
