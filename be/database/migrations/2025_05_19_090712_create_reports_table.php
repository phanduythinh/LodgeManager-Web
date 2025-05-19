<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->string('MaBaoCao', 30)->primary();
            $table->float('DoanhThu');
            $table->float('LoiNhuan');
            $table->integer('SoNguoiThue');
            $table->integer('SoNhaTro');
            $table->integer('SoPhongTrong');
            $table->string('MaChuTro', 30);
            $table->string('MaHopDong', 30)->nullable();

            $table->foreign('MaChuTro')->references('MaChuTro')->on('owners')->onDelete('cascade');
            $table->foreign('MaHopDong')->references('MaHopDong')->on('contracts')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
