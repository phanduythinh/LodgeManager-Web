<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('khach_hangs', function (Blueprint $table) {
            $table->id();
            $table->string('ma_khach_hang')->unique();
            $table->string('ho_ten');
            $table->string('so_dien_thoai');
            $table->string('email')->unique();
            $table->string('cccd')->unique();
            $table->string('gioi_tinh');
            $table->date('ngay_sinh');
            $table->string('dia_chi_nha');
            $table->string('xa_phuong');
            $table->string('quan_huyen');
            $table->string('tinh_thanh');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('khach_hangs');
    }
};
