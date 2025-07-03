<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_shop_open_hours_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopOpenHoursTable extends Migration
{
    public function up(): void
    {
        Schema::create('shop_open_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('day_of_week'); // 0:日曜〜6:土曜
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_open_hours');
    }
}
