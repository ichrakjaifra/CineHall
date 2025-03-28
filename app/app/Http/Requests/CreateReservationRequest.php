<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateReservationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'screening_id' => 'required|exists:screenings,id',
            'seats' => 'required|array|min:1',
            'seats.*' => [
                'required',
                'exists:seats,id',
                Rule::unique('tickets', 'seat_id')->where(function ($query) {
                    return $query->whereIn('reservation_id', function($subQuery) {
                        $subQuery->select('id')
                            ->from('reservations')
                            ->where('screening_id', $this->input('screening_id'))
                            ->where('status', '!=', 'cancelled');
                    });
                })
            ],
        ];
    }

    public function messages()
    {
        return [
            'seats.*.unique' => 'One or more selected seats are already taken',
        ];
    }
}