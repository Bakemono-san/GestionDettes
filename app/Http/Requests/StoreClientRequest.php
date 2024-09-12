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
 *     schema="StoreClientRequest",
 *     type="object",
 *     required={"surname", "telephone"},
 *     @OA\Property(property="surname", type="string", description="Nom du client"),
 *     @OA\Property(property="adresse", type="string", description="Adresse du client"),
 *     @OA\Property(property="telephone", type="string", description="Numéro de téléphone"),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(property="nom", type="string", description="Nom de l'utilisateur associé"),
 *         @OA\Property(property="prenom", type="string", description="Prénom de l'utilisateur associé"),
 *         @OA\Property(property="login", type="string", description="Login de l'utilisateur associé"),
 *         @OA\Property(property="role_id", type="integer", description="Rôle de l'utilisateur"),
 *         @OA\Property(property="password", type="string", description="Mot de passe de l'utilisateur"),
 *         @OA\Property(property="photo", type="string", format="binary", description="Photo de l'utilisateur")
 *     )
 * )
 */
class StoreClientRequest extends FormRequest
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
            'surname' => ['required', 'string', 'max:255','unique:clients,surname'],
            'adresse' => ['string', 'max:255'],
            'telephone' => ['required',new TelephoneRule(),'unique:clients,telephone'],
            'categorie' => ['required','exists:categories,id'],
            'montant_max' => ['numeric', 'min:0', 'required_if:categorie,2'],
            
            'user' => ['sometimes','array'],
            'user.photo' => 'required_with:user|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user.nom' => ['required_with:user','string'],
            'user.prenom' => ['required_with:user','string'],
            'user.login' => ['required_with:user','string'],
            'user.password' => ['required_with:user', new CustumPasswordRule(),'confirmed'],

        ];
/*
        if ($this->filled('user')) {
            $userRules = (new StoreUserRequest())->Rules();
            $rules = array_merge($rules, ['user' => 'array']);
            $rules = array_merge($rules, array_combine(
                array_map(fn($key) => "user.$key", array_keys($userRules)),
                $userRules
            ));
        }
*/
      //  dd($rules);

        return $rules;
    }

    function messages()
    {
        return [
            'surname.required' => "Le surnom est obligatoire.",
            'surname.string' => "Le surnom doit être une chaîne de caractères.",
            'surname.max' => "Le surnom ne doit pas dépasser 255 caractères.",
            'surname.unique' => "Ce surnom est déjà utilisé.",
            'address.string' => "L'adresse doit être une chaîne de caractères.",
            'address.max' => "L'adresse ne doit pas dépasser 255 caractères.",
            'telephone.required' => "Le téléphone est obligatoire.",
            'telephone.telephone' => "Le téléphone doit être valide.",
            'photo.required' => "La photo est obligatoire.",
            'photo.image' => "La photo doit être une image.",
            'photo.mimes' => "La photo doit être au format jpeg, png, jpg ou gif.",
            'photo.max' => "La photo ne doit pas dépasser 2048 Ko.",
            'categorie.required' => "La catégorie est obligatoire.",
            'categorie.exists' => "La catégorie n'existe pas.",
            'montant_max.numeric' => "Le montant maximum doit être un nombre.",
            'montant_max.min' => "Le montant maximum doit être supérieur ou égal à 0.",
            'montant_max.required_if' => "Le montant maximum est obligatoire si la catégorie est '2'.",
            'telephone.unique' => "Ce téléphone est déjà utilisé."
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse(["erreur" => $validator->errors()],StateEnum::ECHEC,"erreur de validation",411));
    }
}
