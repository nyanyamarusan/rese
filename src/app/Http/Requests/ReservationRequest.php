<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
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
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exits = Reservation::where('shop_id', $this->shop_id)
                        ->where('date', $this->date)
                        ->where('time', $value)
                        ->exists();
                    if ($exits) {
                        $fail('選択された時間はすでに予約されています');
                    }
                },
            ],
            'number' => ['required', 'numeric', 'min:1', 'max:10'],
        ];
    }

    public function messages()
    {
        return [
            'date.required' => '日付を選択してください',
            'time.required' => '時間を選択してください',
            'number.required' => '人数を選択してください',
        ];
    }
}
