<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Rules\CustumPasswordRule;
use App\Rules\TelephoneRule;
use App\Traits\RestResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *     schema="StoreArticleRequest",
 *     type="object",
 *     required={"libelle", "user_id", "prix", "quantite"},
 *     @OA\Property(property="libelle", type="string", maxLength=255, example="Article X"),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="prix", type="number", example=100.50),
 *     @OA\Property(property="quantite", type="integer", example=10)
 * )
 */
class StoreArticleRequest extends FormRequest
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
        $rules = [
            'libelle' => ['required', 'string', 'max:255','unique:articles,libelle'],
            'user_id' => ['required', 'numeric', 'max:255','exists:users,id'],
            'prix' => ['required', 'numeric','min:1'],
            'quantite' => ['required', 'numeric','min:1'],

        ];


        return $rules;
    }

    function messages()
    {
        return [
            'libelle.required' => "Le libelle est obligatoire.",
            'libelle.unique' => 'Le libelle doit etre unique',
            'user_id.required' => 'L\'utilisateur est obligatoire.',
            'user_id.exists' => 'L\'utilisateur n\'existe pas.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit etre un nombre.',
            'prix.min' => 'Le prix doit etre un nombre supérieur ou égale à 1.',
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.numeric' => 'La quantité doit etre un nombre.',
            'quantite.min' => 'La quantité doit être supérieure ou égale à 1.'
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(),StateEnum::ECHEC,"erreur de validation",411));
    }
}
