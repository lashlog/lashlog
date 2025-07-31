<?php
// app/Usecases/Reservation/StoreReservationUsecase.php

namespace App\UseCases\Reservation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Reservation;
use App\Services\ReservationDomainService;
use Illuminate\Http\Request;

class StoreReservationUseCase
{
    protected $reservationDomainService;

    public function __construct(ReservationDomainService $reservationDomainService)
    {
        $this->reservationDomainService = $reservationDomainService;
    }

    public function execute(Request $request)
    {
        $request->merge(['shop_id' => auth('shop')->id()]);

        // バリデーション
        $validator = Validator::make($request->all(), [
            'reserved_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'staff_id' => 'required|exists:staffs,id',
            'customer_id' => 'nullable|exists:customers,id',
            'menu_id' => 'required|exists:menus,id',
            'memo' => 'nullable|string',
            'shop_id' => 'required|exists:shops,id',
            'reservation_source_id' => 'nullable|exists:reservation_sources,id',
        ], [
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
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $validated = $validator->validated();

        // 重複チェック
        if ($this->reservationDomainService->hasConflict($validated)) {
            throw ValidationException::withMessages([
                'start_time' => ['この時間帯はすでに予約が入っています。'],
            ]);
        }

        // 営業時間チェック
        if (!$this->reservationDomainService->isWithinBusinessHours($validated)) {
            throw ValidationException::withMessages([
                'start_time' => ['営業時間外の予約です。'],
            ]);
        }

        return Reservation::create($validated);
    }
}
