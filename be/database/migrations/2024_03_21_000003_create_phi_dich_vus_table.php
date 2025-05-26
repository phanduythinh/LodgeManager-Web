<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('phi_dich_vus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toa_nha_id')->constrained('toa_nhas')->onDelete('cascade');
            $table->string('ma_dich_vu')->unique();
            $table->string('ten_dich_vu');
            $table->string('loai_dich_vu');
            $table->decimal('don_gia', 12, 2);
            $table->string('don_vi_tinh');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('phi_dich_vus');
    }
};
