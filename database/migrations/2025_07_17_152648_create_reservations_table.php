<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('staffs')->onDelete('cascade');
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->foreignId('reservation_source_id')->nullable()->constrained('reservation_sources')->nullOnDelete();
            $table->date('reserved_date'); // 予約日
            $table->time('start_time');         // 例: 09:00
            $table->time('end_time');           // 例: 10:30
            $table->integer('duration_minutes')->nullable();  // 例: 90
            $table->integer('price')->nullable(); // 価格
            $table->boolean('is_first_time')->nullable(); // 初回予約かどうか
            $table->string('reservation_source_name')->nullable();
            $table->enum('reservation_status', ['confirmed', 'cancelled', 'no_show'])->default('confirmed'); // 予約ステータス
            $table->string('menu_name_snapshot')->nullable(); // メニュー名のスナップショット
            $table->string('staff_name_snapshot')->nullable(); // スタッフ名のスナップショット
            $table->text('memo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
