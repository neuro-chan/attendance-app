<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UserCorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clock_in' => ['required', 'date_format:H:i'],
            'clock_out' => ['required', 'date_format:H:i'],
            'breaks.*.break_id' => ['nullable', 'integer'],  // 追加
            'breaks.*.break_start' => ['nullable', 'date_format:H:i'],
            'breaks.*.break_end' => ['nullable', 'date_format:H:i'],
            'note' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'clock_in.required' => '出勤時間を入力してください',
            'clock_in.date_format' => '出勤時間は HH:mm 形式で入力してください',
            'clock_out.required' => '退勤時間を入力してください',
            'clock_out.date_format' => '退勤時間は HH:mm 形式で入力してください',
            'breaks.*.break_start.date_format' => '休憩開始時間は HH:mm 形式で入力してください',
            'breaks.*.break_end.date_format' => '休憩終了時間は HH:mm 形式で入力してください',
            'note.required' => '備考を記入してください',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $clockIn = $this->toMinutes($this->input('clock_in'));
            $clockOut = $this->toMinutes($this->input('clock_out'));

            // 1) 出勤 >= 退勤
            if ($clockIn !== null && $clockOut !== null && $clockIn >= $clockOut) {
                $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です');
            }

            // 2) 3) 休憩の複数行チェック
            foreach ($this->input('breaks', []) as $i => $break) {
                $breakStart = $this->toMinutes($break['break_start'] ?? null);
                $breakEnd = $this->toMinutes($break['break_end'] ?? null);

                if ($breakStart !== null && $breakEnd !== null && $breakStart >= $breakEnd) {
                    $validator->errors()->add("breaks.$i.break_start", '休憩時間が不適切な値です');
                }
                if ($breakStart !== null && $clockIn !== null && $breakStart < $clockIn) {
                    $validator->errors()->add("breaks.$i.break_start", '休憩時間が不適切な値です');
                }
                if ($breakStart !== null && $clockOut !== null && $breakStart > $clockOut) {
                    $validator->errors()->add("breaks.$i.break_start", '休憩時間が不適切な値です');
                }
                if ($breakEnd !== null && $clockIn !== null && $breakEnd < $clockIn) {
                    $validator->errors()->add("breaks.$i.break_end", '休憩時間が不適切な値です');
                }
                if ($breakEnd !== null && $clockOut !== null && $breakEnd > $clockOut) {
                    $validator->errors()->add("breaks.$i.break_end", '休憩時間もしくは退勤時間が不適切な値です');
                }
            }
        });
    }

    private function toMinutes(?string $time): ?int
    {
        if (blank($time) || ! preg_match('/^\d{2}:\d{2}$/', $time)) {
            return null;
        }

        [$h, $m] = array_map('intval', explode(':', $time));

        return $h * 60 + $m;
    }
}
