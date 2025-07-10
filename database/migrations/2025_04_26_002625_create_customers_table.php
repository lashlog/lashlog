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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // 紐付け
            $table->foreignId('shop_id')->constrained()->onDelete('cascade'); // 店舗に紐づく
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // 基本情報
            $table->string('name');          // 顧客名（フルネーム）
            $table->string('phone')->nullable(); // 電話番号
            $table->string('email')->nullable(); // メールアドレス

            // 任意の追加情報
            $table->date('birthday')->nullable();  // 誕生日（顧客理解のため）
            // $table->string('sns_account')->nullable(); // LINEやInstagramのID等 後から別テーブル作成して管理予定

            // 健康・施術履歴関連
            $table->text('allergy_notes')->nullable();   // アレルギー内容（任意記入）
            $table->text('surgery_notes')->nullable();   // 手術歴（任意記入）
            $table->text('memo')->nullable();            // その他メモ（髪質、対応履歴など）

            // 管理用
            $table->timestamps(); // created_at / updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
