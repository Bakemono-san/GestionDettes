<?php

namespace App\Http\Requests;

use App\Enums\StateEnum;
use App\Rules\CustumPasswordRule;
use App\Traits\RestResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *     schema="userForClientRequest",
 *     type="object",
 *     required={"login", "password", "nom", "prenom", "etat", "role_id", "client_id"},
 *     properties={
 *         @OA\Property(property="login", type="string", description="Login for the user"),
 *         @OA\Property(property="password", type="string", description="Password for the user"),
 *         @OA\Property(property="nom", type="string", description="User's last name"),
 *         @OA\Property(property="prenom", type="string", description="User's first name"),
 *         @OA\Property(property="etat", type="string", description="User's status", enum={"true", "false"}),
 *         @OA\Property(property="role_id", type="integer", description="Role ID for the user"),
 *         @OA\Property(property="client_id", type="integer", description="Client ID to associate with the user")
 *     }
 * )
 */
class userForClientRequest extends FormRequest
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
            "login" => "required|string|max:255|unique:users,login",
            "password" => ["confirmed",new CustumPasswordRule()],
            "nom" => "required|string|max:255",
            "prenom" => "required|string|max:255",
            "etat" => "required|in:true,false",
            // "role_id" => "required|exists:roles,id",
            "client_id" => "required|exists:clients,id"
        ];
    }

    public function messages(): array{
        return [
            "login.required" => "Le login est obligatoire.",
            "login.unique" => "Ce login est déjà utilisé.",
            "password.confirmed" => "Les mots de passe ne correspondent pas.",
            "nom.required" => "Le nom est obligatoire.",
            "prenom.required" => "Le prénom est obligatoire.",
            "etat.required" => "L'état est obligatoire.",
            "etat.boolean" => "L'état doit être une valeur booléenne.",
            "role_id.required" => "Le rôle est obligatoire.",
            "role_id.exists" => "Ce rôle n'existe pas.",
            "client_id.required" => "Le client est obligatoire.",
            "client_id.exists" => "Ce client n'existe pas."
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse(["erreur" => $validator->errors()],StateEnum    ::ECHEC,"erreur de validation",411));
    }
}
