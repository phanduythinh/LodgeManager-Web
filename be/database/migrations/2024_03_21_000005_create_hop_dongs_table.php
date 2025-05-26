<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hop_dongs', function (Blueprint $table) {
            $table->id();
            $table->string('ma_hop_dong')->unique();
            $table->foreignId('phong_id')->constrained('phongs')->onDelete('cascade');
            $table->date('ngay_bat_dau');
            $table->date('ngay_ket_thuc');
            $table->decimal('tien_thue', 12, 2);
            $table->decimal('tien_coc', 12, 2);
            $table->string('chu_ky_thanh_toan');
            $table->date('ngay_tinh_tien');
            $table->string('trang_thai');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hop_dongs');
    }
};
