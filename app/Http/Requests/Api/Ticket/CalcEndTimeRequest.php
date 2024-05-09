<?php

namespace App\Http\Requests\Api\Ticket;

use App\Http\Controllers\Api\TicketController;
use Illuminate\Foundation\Http\FormRequest;

class CalcEndTimeRequest extends FormRequest
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
            'start' => 'required|date_format:'.TicketController::INPUT_STARTDATE_FORMAT,
            'estimate' => 'required|integer|min:0',
            'workdays' => 'nullable|boolean',
            'settings' => 'nullable|integer|min:0',
            'hoursStart' => 'required|array:hours,minutes',
            'hoursEnd' => 'required|array:hours,minutes',
        ];
    }
}
