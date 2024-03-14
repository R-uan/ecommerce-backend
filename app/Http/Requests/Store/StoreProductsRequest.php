<?php

namespace App\Http\Requests\Store;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

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
      'name'              => ['required'],
      'image_url'         => ['url'],
      'category'          => ['required', 'string'],
      'availability'      => ['required'],
      'unit_price'        => ['required'],
      'production_time'   => ['required'],
      'manufacturers_id'  => ['required', 'numeric'],
      'short_description' => ['required'],
      'long_description'  => ['required'],

      'product_details'   => [
        'energy_system'       => ['required'],
        'landing_system'      => ['required'],
        'emergency_system'    => ['required'],
        'propulsion_system'   => ['required'],
        'navigation_system'   => ['required'],
        'external_structure'  => ['required'],
        'termic_protection'   => ['required'],
        'comunication_system' => ['required'],
      ],
    ];
  }

  protected function failedValidation(Validator $validator) {
    throw new HttpResponseException(
      response()->json([
        'message' => $validator->errors(),
      ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
    );
  }
}
