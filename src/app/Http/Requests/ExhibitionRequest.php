<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
      'name'         => ['required', 'string'],
      'description'  => ['required', 'string', 'max:255'],
      'image'        => ['required', 'image', 'mimes:jpeg,png'],
      'category_ids' => ['required', 'array'],
      'condition_id' => ['required', 'integer'],
      'price'        => ['required', 'integer', 'min:0'],
    ];
  }

  public function messages()
  {
    return [
      'name.required'         => '商品名は必須です。',
      'description.required'  => '商品説明は必須です。',
      'description.max'       => '商品説明は255文字以内で入力してください。',
      'image.required'        => '商品画像を選択してください。',
      'image.mimes'           => '商品画像はjpegまたはpng形式でアップロードしてください。',
      'category_ids.required' => '商品のカテゴリーを選択してください。',
      'condition_id.required' => '商品の状態を選択してください。',
      'price.required'        => '商品価格を入力してください。',
      'price.integer'         => '商品価格は数値で入力してください。',
      'price.min'             => '商品価格は0円以上で入力してください。',
    ];
  }
}
