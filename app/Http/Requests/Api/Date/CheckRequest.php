<?php

namespace App\Http\Requests\Api\Date;

use App\Http\Controllers\Api\DateController;
use Illuminate\Foundation\Http\FormRequest;

class CheckRequest extends FormRequest
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
            'date' => 'required|date_format:'.DateController::INPUT_DATE_FORMAT,
            'settings' => 'required|integer|min:0',
        ];
    }
}
