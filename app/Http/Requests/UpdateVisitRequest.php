<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'visitor_id' => 'sometimes|exists:visitors,id',
            'host_employee_id' => 'sometimes|exists:employees,id',
            'reason_of_visit' => 'nullable|string|max:1000',
            'expected_arrival_time' => 'sometimes|date',
            'expected_departure_time' => 'nullable|date|after_or_equal:expected_arrival_time',
            'check_in_time' => 'nullable|date',
            'check_out_time' => 'nullable|date',
        ];
    }
}
