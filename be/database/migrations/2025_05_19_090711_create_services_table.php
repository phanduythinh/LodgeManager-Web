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
        Schema::create('services', function (Blueprint $table) {
            $table->string('MaDichVu', 30)->primary();
            $table->string('TenDichVu', 50);
            $table->string('DonViTinh', 10);
            $table->float('DonGia');
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
        Schema::dropIfExists('services');
    }
};
