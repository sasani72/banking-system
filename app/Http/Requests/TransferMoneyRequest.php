<?php

namespace App\Http\Requests;

use App\Rules\NotIdentical;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TransferMoneyRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'origin_account' => 'required|exists:accounts,uuid',
            'destination_account' => ['required','exists:accounts,uuid', new NotIdentical],
            'amount' => 'required|integer|min:10',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => "Validation Error",
            'data' => $validator->errors()
        ]));
    }
}
