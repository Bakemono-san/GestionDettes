<?php

namespace App\Http\Requests;

use App\Enums\StateEnum;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidPaymentAmount;
use App\Traits\RestResponseTrait;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *     schema="StorePaiementRequest",
 *     type="object",
 *     required={"montant"},
 *     properties={
 *         @OA\Property(
 *             property="montant",
 *             type="number",
 *             format="float",
 *             description="Amount of the payment",
 *             example=100.50
 *         )
 *     }
 * )
 */
class StorePaiementRequest extends FormRequest
{
    use RestResponseTrait;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Return true if authorization logic is not required
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $detteId = $this->route('id');
        return [
            'montant' => ['required', 'numeric', 'min:0', new ValidPaymentAmount($detteId)],
        ];
    }

    /**
     * Get the custom error messages for the validator.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'montant.required' => 'The payment amount is required.',
            'montant.numeric' => 'The payment amount must be a number.',
            'montant.min' => 'The payment amount must be positive.',
            'dette_id.required' => 'The debt ID is required.',
            'dette_id.exists' => 'The selected debt does not exist.',
        ];
    }

    public function failedValidation(ValidationValidator $validator){
        throw new HttpResponseException($this->sendResponse(["erreur" => $validator->errors()],StateEnum::ECHEC,"erreur de validation",411));
    }
}
