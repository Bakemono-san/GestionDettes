<?php

namespace App\Http\Requests;

use App\Enums\StateEnum;
use App\Models\User;
use App\Rules\CustumPasswordRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(User $user): bool
    {
        // if($user->role == 'admin'){
        //     return true;
        // }
        // return false;
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
            'nom' => 'nullable|string|max:255',
            'prenom' => 'nullable|string',
            'email' => 'nullable|email|unique:users',
            'login' => 'nullable|string|unique:users',
            'password' => ['nullable|', new CustumPasswordRule()],
            'role' => 'nullable|in:admin|boutiquier',
        ];
    }

    public function messages()
    {
        return [
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'login.string' => 'Le login doit être une chaîne de caractères.',
            'login.unique' => 'Ce login est déjà utilisé.',
            'password.nullable' => 'Le mot de passe est facultatif.',
            'password' => 'Le mot de passe ne respecte pas les critères requis.',
            'role.in' => 'Le rôle doit être l\'un des suivants : admin, boutiquier.',
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse(["erreur" => $validator->errors()],StateEnum::ECHEC,"erreur de validation",411));
    }
}
