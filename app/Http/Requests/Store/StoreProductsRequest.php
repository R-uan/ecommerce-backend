<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductsRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'name' => ['required'],
            'description' => ['required'],
            'image_url' => ['url'],
            'category' => ['required', 'string'],
            'availability' => ['required'],
            'unit_price' => ['required'],
            'manufacturers_id' => ['required', 'numeric'],
        ];
    }
}
