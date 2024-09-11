<?php

namespace App\Http\Requests;

use App\Enums\StateEnum;
use App\Rules\CustumPasswordRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class userForClientRequest extends FormRequest
{
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
            "role_id" => "required|exists:roles,id",
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
