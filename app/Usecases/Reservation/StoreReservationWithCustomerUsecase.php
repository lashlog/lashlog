<?php

namespace App\UseCases\Reservation;

use App\Repositories\CustomerRepository;
use App\Repositories\ReservationRepository;
use App\Services\ReservationDomainService;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;

class StoreReservationWithCustomerUseCase
{
    public function __construct(
        private CustomerRepository $customerRepo,
        private ReservationRepository $reservationRepo,
        private ReservationDomainService $reservationDomainService
    ) {}

    public function execute(array $data, int $shopId): Reservation
    {
        return DB::transaction(function () use ($data, $shopId) {
            // 顧客IDがある場合：既存顧客
            if (!empty($data['customer_id'])) {
                $customerId = $data['customer_id'];
            } else {
                // 顧客IDがない場合：新規顧客を作成
                $customer = $this->customerRepo->create([
                    'shop_id' => $shopId,
                    'name' => $data['customer_name'],
                    'phone' => $data['customer_phone'],
                ]);
                $customerId = $customer->id;
            }

            // 検証対象データをまとめる
            $checkData = [
                'shop_id'       => $shopId,
                'staff_id'      => $data['staff_id'],
                'reserved_date' => $data['reserved_date'],
                'start_time'    => $data['start_time'],
                'end_time'      => $data['end_time'],
            ];

            // 営業時間チェック
            if (! $this->reservationDomainService->isWithinBusinessHours($checkData)) {
                throw new \DomainException('営業時間外です');
            }

            // 重複チェック
            if ($this->reservationDomainService->hasConflict($checkData)) {
                throw new \DomainException('予約が重複しています');
            }

            // 予約作成
            return $this->reservationRepo->create([
                'shop_id'       => $shopId,
                'customer_id'   => $customerId,
                'menu_id'       => $data['menu_id'],
                'staff_id'      => $data['staff_id'],
                'reserved_date' => $data['reserved_date'],
                'start_time'    => $data['start_time'],
                'end_time'      => $data['end_time'],
                'memo' => $data['memo'] ?? null,
                'reservation_source_id' => $data['reservation_source_id'] ?? null,
            ]);
        });
    }
}
