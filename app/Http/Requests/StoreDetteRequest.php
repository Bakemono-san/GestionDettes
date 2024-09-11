<?php

namespace App\Http\Requests;

use App\Enums\StateEnum;
use App\Traits\RestResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *     schema="StoreDetteRequest",
 *     type="object",
 *     required={"montant", "client_id", "articles"},
 *     properties={
 *         @OA\Property(property="montant", type="number"),
 *         @OA\Property(property="client_id", type="integer"),
 *         @OA\Property(
 *             property="articles",
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="quantite", type="integer"),
 *                 @OA\Property(property="prixVente", type="number")
 *             )
 *         ),
 *         @OA\Property(property="paiement", type="object")
 *     }
 * )
 */
class StoreDetteRequest extends FormRequest
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
            'client_id' => 'required|exists:clients,id',
            'articles' => 'required|array|min:1',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1',
            'articles.*.prixVente' => 'required|numeric|min:1',
            'paiement' => 'sometimes|array',
            'paiement.montant' => ['required_with:paiement', 'numeric', 'min:0', function ($attribute, $value, $fail) {
                $montant = $this->input('paiement.montant');
                if ($value > $montant) {
                    $fail('The montant in paiement must be less than or equal to the montant.');
                }
            }]
        ];
    }

    public function messages(): array
    {
        return [
            'montant.required' => 'Le montant de la dette est obligatoire.',
            'montant.numeric' => 'Le montant de la dette doit être un nombre.',
            'clients.required' => 'Le client est obligatoire.',
            'clients.exists' => 'Le client n\'existe pas.',
            'articles.required' => 'Au moins un article est requis.',
            'articles.*.id.required' => 'L\'id de l\'article est obligatoire.',
            'articles.*.id.exists' => 'L\'article avec l\'id :attribute n\'existe pas.',
            'articles.*.quantite.required' => 'La quantité de l\'article :attribute est obligatoire.',
            'articles.*.quantite.integer' => 'La quantité de l\'article :attribute doit être un entier.',
            'articles.*.quantite.min' => 'La quantité de l\'article :attribute doit être supérieure ou égale à 1.',
            'articles.*.prixVente.required' => 'Le prix de vente de l\'article :attribute est obligatoire.',
            'articles.*.prixVente.numeric' => 'Le prix de vente de l\'article :attribute doit être un nombre.',
            'articles.*.prixVente.min' => 'Le prix de vente de l\'article :attribute doit être supérieur ou égale à 0.',
            'paiement.required' => 'Le montant de la dette est obligatoire.',
            'paiement.numeric' => 'Le montant de la dette doit être un nombre.',
            'paiement.min' => 'Le montant de la dette doit être supérieur ou égale à 1.',
            'paiement.montant' => 'Le montant du paiement doit être inférieur ou égal au montant de la dette.'
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse(["erreur" => $validator->errors()], StateEnum::ECHEC, "erreur de validation", 411));
    }
}
