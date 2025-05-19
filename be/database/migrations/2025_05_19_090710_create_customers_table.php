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
        Schema::create('customers', function (Blueprint $table) {
            $table->string('MaKhachHang', 10)->primary();
            $table->string('TenKhachHang', 30);
            $table->string('SDT', 10);
            $table->string('Email', 50)->unique();
            $table->string('DiaChiThuongTru', 50);
            $table->date('NgaySinh');
            $table->string('CCCD', 50);
            $table->string('MaChuTro', 30);

            $table->foreign('MaChuTro')->references('MaChuTro')->on('owners')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
