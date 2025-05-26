<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('toa_nhas', function (Blueprint $table) {
            $table->id();
            $table->string('ma_nha')->unique();
            $table->string('ten_nha');
            $table->string('dia_chi_nha');
            $table->string('xa_phuong');
            $table->string('quan_huyen');
            $table->string('tinh_thanh');
            $table->string('trang_thai');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('toa_nhas');
    }
};
