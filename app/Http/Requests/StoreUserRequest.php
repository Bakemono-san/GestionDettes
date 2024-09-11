<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Enums\UserRole;
use App\Rules\CustumPasswordRule;
use App\Rules\PasswordRules;
use App\Traits\RestResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *     schema="StoreUserRequest",
 *     type="object",
 *     required={"nom", "prenom", "login", "role_id", "etat", "password"},
 *     properties={
 *         @OA\Property(property="nom", type="string", description="User's last name"),
 *         @OA\Property(property="prenom", type="string", description="User's first name"),
 *         @OA\Property(property="login", type="string", description="Login for the user"),
 *         @OA\Property(property="role_id", type="integer", description="Role ID for the user"),
 *         @OA\Property(property="etat", type="string", description="User's status", enum={"true", "false"}),
 *         @OA\Property(property="password", type="string", description="Password for the user"),
 *         @OA\Property(property="password_confirmation", type="string", description="Password confirmation")
 *     }
 * )
 */
class StoreUserRequest extends FormRequest
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
    public function Rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'login' => 'required|string|max:255|unique:users,login',
            'role_id' => ['required', 'exists:roles,id'],
            // 'email' => 'required|email|unique:users,email',
            'etat' => 'required|in:true,false',
            'password' => ['confirmed', new CustumPasswordRule()],
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function validationMessages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'role_id.required' => 'Le rôle est obligatoire.',
            'role_id.exists' => 'Ce role n\'existe pas.',
            'email.required' => "L'email est obligatoire.",
            'email.email' => "L'email doit être une adresse email valide.",
            'email.unique' => "Cet email est déjà utilisé.",
            'login.required' => 'Le login est obligatoire.',
            'login.unique' => "Cet login est déjà utilisé.",
            'etat.required' => 'L\'état est obligatoire.',
            'etat.boolean' => 'L\'état doit être une valeur booléenne.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'photo.required' => 'La photo est obligatoire.',
            'photo.image' => "La photo doit être une image.",
            'photo.mimes' => "La photo doit être au format jpeg, png, jpg ou gif.",
            'photo.max' => "La photo ne doit pas dépasser 2048 Ko.",
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(), StateEnum::ECHEC, "erreur de validation", 411));
    }
}
