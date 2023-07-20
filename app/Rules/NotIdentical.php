<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class NotIdentical implements DataAwareRule ,ValidationRule
{
    protected $data = [];

    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === $this->data['origin_account']) {
            $fail('The :attribute should not be identical to the origin_account while transferring money.');
        }
    }
}
