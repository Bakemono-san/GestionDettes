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
            'telephone' => ['required',new TelephoneRule()],
            
            'user' => ['sometimes','array'],
            'user.photo' => 'required_with:user|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user.nom' => ['required_with:user','string'],
            'user.prenom' => ['required_with:user','string'],
            'user.login' => ['required_with:user','string'],
            'user.role_id' => ['required_with:user','integer', 'exists:roles,id'],
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
            'user.role_id.exists' => "Le role doit exister.",
            'user.role_id.required_with' => "Le role est obligatoire.",
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse(["erreur" => $validator->errors()],StateEnum::ECHEC,"erreur de validation",411));
    }
}
