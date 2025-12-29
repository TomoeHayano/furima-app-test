<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class TransactionMessageRequest extends FormRequest
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

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'body'  => ['required', 'string', 'max:400'],
            'image' => ['nullable', 'file', 'mimes:jpeg,png'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'body.required' => '本文を入力してください',
            'body.max'      => '本文は400文字以内で入力してください',
            'image.mimes'   => '「.png」または「.jpeg」形式でアップロードしてください',
        ];
    }
}
