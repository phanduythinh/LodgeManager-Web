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
        Schema::create('contracts', function (Blueprint $table) {
            $table->string('MaHopDong', 30)->primary();
            $table->string('NguoiThue', 30);
            $table->date('NgayBatDau');
            $table->date('NgayKetThuc');
            $table->float('TienCoc');
            $table->float('TienPhong');
            $table->string('SoPhong', 50);
            $table->string('SoNha', 50);
            $table->string('MaChuTro', 30);
            $table->string('MaNhaTro', 30);
            $table->string('MaPhong', 30);

            $table->foreign('MaChuTro')->references('MaChuTro')->on('owners')->onDelete('cascade');
            $table->foreign('MaNhaTro')->references('MaNhaTro')->on('buildings')->onDelete('cascade');
            $table->foreign('MaPhong')->references('MaPhong')->on('rooms')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
