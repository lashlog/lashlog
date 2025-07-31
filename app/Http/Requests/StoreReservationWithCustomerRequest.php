<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationWithCustomerRequest extends FormRequest
{
    /**
     * 認可ロジック（今回は true 固定でOK）
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール定義
     */
    public function rules(): array
    {
        return [
            'reserved_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'staff_id' => 'required|exists:staffs,id',
            'customer_id' => 'nullable|exists:customers,id',
            'menu_id' => 'required|exists:menus,id',
            'memo' => 'nullable|string',
            'shop_id' => 'required|exists:shops,id',
            'reservation_source_id' => 'nullable|exists:reservation_sources,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
        ];
    }

    /**
     * カスタムエラーメッセージ
     */
    public function messages(): array
    {
        return [
            'reserved_date.required' => '予約日を入力してください。',
            'reserved_date.date' => '予約日には正しい日付を入力してください。',
            'start_time.required' => '開始時刻を入力してください。',
            'start_time.date_format' => '開始時刻の形式が不正です（例: 09:00）。',
            'end_time.required' => '終了時刻を入力してください。',
            'end_time.date_format' => '終了時刻の形式が不正です（例: 10:30）。',
            'end_time.after' => '終了時刻は開始時刻より後にしてください。',
            'staff_id.required' => '担当スタッフを選択してください。',
            'staff_id.exists' => '選択されたスタッフが存在しません。',
            'customer_id.exists' => '選択された顧客が存在しません。',
            'menu_id.required' => 'メニューを選択してください。',
            'menu_id.exists' => '選択されたメニューが存在しません。',
            'memo.string' => 'メモは文字列で入力してください。',
            'shop_id.required' => '店舗IDが必要です（システムエラー）。',
            'shop_id.exists' => '店舗が存在しません（システムエラー）。',
            'reservation_source_id.exists' => '予約元媒体の情報が正しくありません。',
            'customer_name.required' => '顧客名を入力してください。',
            'customer_name.string' => '顧客名は文字列で入力してください。',
            'customer_phone.string' => '電話番号は文字列で入力してください。',
        ];
    }
}
