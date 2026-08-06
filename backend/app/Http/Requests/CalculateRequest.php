<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CalculateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'formula' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (!is_string($value) || !str_contains($value, '[OMIE_MD]')) {
                        $fail('La fórmula debe incluir la etiqueta obligatoria [OMIE_MD].');
                    }
                },
            ],
        ];
    }

    /**
     * Retornar código HTTP 400 en lugar de 422 para cumplir estrictamente con la especificación de API.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'error' => 'Datos de entrada inválidos o incompletos.',
                'details' => $validator->errors(),
            ], 400)
        );
    }
}
