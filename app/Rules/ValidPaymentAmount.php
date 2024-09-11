<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Dette;

class ValidPaymentAmount implements Rule
{
    protected $detteId;
    
    /**
     * Create a new rule instance.
     *
     * @param int $detteId
     * @return void
     */
    public function __construct($detteId)
    {
        $this->detteId = $detteId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Ensure the value is numeric and positive
        if (!is_numeric($value) || $value <= 0) {
            return false;
        }

        // Fetch the debt to check the remaining amount
        $dette = Dette::find($this->detteId);
        if (!$dette) {
            return false;
        }

        // Check if the amount is less than or equal to the remaining amount
        return $value <= $dette->montant_restant;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The payment amount must be numeric, positive, and less than or equal to the remaining amount of the debt.';
    }
}
