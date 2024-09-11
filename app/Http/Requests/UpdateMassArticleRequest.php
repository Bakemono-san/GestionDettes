<?php

namespace App\Http\Requests;

use App\Enums\StateEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMassArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:articles,id',
            'quantite' => 'required|numeric|min:1',
        ];
    }

    public function messages(){
        return [
            'id.required' => 'L\'id de l\'article est obligatoire',
            'id.exists' => 'L\'article n\'existe pas',
            'quantite.required' => 'La quantité est obligatoire',
            'quantite.numeric' => 'La quantité doit etre un nombre',
            'quantite.min' => 'La quantité doit être supérieure ou égale à 1'
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse(["erreur" => $validator->errors()],StateEnum::ECHEC,"erreur de validation",411));
    }
}
