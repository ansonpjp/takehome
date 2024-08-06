<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PurchaseOrderTotalsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'purchase_order_ids' => 'required|array',
            'purchase_order_ids.*' => 'integer',
        ];
    }

    /*
     * Validation messages to the customer
     */
    public function messages()
    {
        return [
            'purchase_order_ids.required' => 'The purchase order Id field is required',
            'purchase_order_ids.array' => 'The purchase order Ids must be array',
            'purchase_order_ids.*' => 'Each purchase order Id should be integer'
        ];
    }

    /*
    * failed validations to respond to the customer
    */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422));
    }
}
