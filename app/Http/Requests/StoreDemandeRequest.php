<?php

namespace App\Http\Requests;

use App\Enums\StateEnum;
use App\Traits\RestResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDemandeRequest extends FormRequest
{
    use RestResponseTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'montant' => 'required|numeric|min:1',
            'articles' => 'required|array|min:1',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'montant.required' => 'Le montant de la dette est obligatoire.',
            'montant.numeric' => 'Le montant de la dette doit être un nombre.',
            'articles.required' => 'Au moins un article est requis.',
            'articles.*.id.required' => 'L\'id de l\'article est obligatoire.',
            'articles.*.id.exists' => 'L\'article avec l\'id :attribute n\'existe pas.',
            'articles.*.quantite.required' => 'La quantité de l\'article :attribute est obligatoire.',
            'articles.*.quantite.integer' => 'La quantité de l\'article :attribute doit être un entier.',
            'articles.*.quantite.min' => 'La quantité de l\'article :attribute doit être supérieure ou égale à 1.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse(["erreur" => $validator->errors()], StateEnum::ECHEC, "erreur de validation", 411));
    }
}
