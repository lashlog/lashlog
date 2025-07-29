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
        Schema::create('reservation_sources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id'); // サロンごとにカスタマイズできるように
            $table->string('name');               // 表示名（例：ミニモ、Instagramなど）
            $table->boolean('is_active')->default(true); // 無効化できるように
            $table->boolean('system_reserved')->default(false);

            $table->timestamps();

            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->index(['shop_id', 'is_active']);
            $table->unique(['shop_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_sources');
    }
};
