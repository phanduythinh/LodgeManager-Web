<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hop_dong_dich_vu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hop_dong_id')->constrained('hop_dongs')->onDelete('cascade');
            $table->foreignId('phi_dich_vu_id')->constrained('phi_dich_vus')->onDelete('cascade');
            $table->string('ma_cong_to')->nullable();
            $table->decimal('chi_so_dau', 12, 2)->nullable();
            $table->date('ngay_tinh_phi')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hop_dong_dich_vu');
    }
};
