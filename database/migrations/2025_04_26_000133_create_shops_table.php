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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('map_url')->nullable();
            $table->time('weekday_open_time')->nullable();
            $table->time('weekday_close_time')->nullable();
            $table->time('weekend_open_time')->nullable();
            $table->time('weekend_close_time')->nullable();
            $table->string('closed_days')->nullable(); // 例: "1,3,7"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
