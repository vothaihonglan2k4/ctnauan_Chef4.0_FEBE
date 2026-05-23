<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1000',
            'payment_method' => 'required|in:credit_card,bank_transfer,momo,vnpay,stripe'
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Số tiền là bắt buộc',
            'amount.numeric' => 'Số tiền phải là số',
            'amount.min' => 'Số tiền tối thiểu là 1,000 VNĐ',
            'payment_method.required' => 'Phương thức thanh toán là bắt buộc',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ'
        ];
    }
}
