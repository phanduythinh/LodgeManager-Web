<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('phongs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toa_nha_id')->constrained('toa_nhas')->onDelete('cascade');
            $table->string('ma_phong')->unique();
            $table->string('ten_phong');
            $table->string('tang');
            $table->decimal('gia_thue', 12, 2);
            $table->decimal('dat_coc', 12, 2);
            $table->decimal('dien_tich', 8, 2);
            $table->integer('so_khach_toi_da');
            $table->string('trang_thai');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('phongs');
    }
};
