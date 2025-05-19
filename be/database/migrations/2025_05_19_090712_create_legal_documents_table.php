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
        Schema::create('legal_documents', function (Blueprint $table) {
            $table->string('MaGiayTo', 30)->primary();
            $table->string('MaKhachHang', 10);
            $table->string('TenKhachHang', 30);
            $table->string('QueQuan', 50);
            $table->string('TinhTrang', 50);

            $table->foreign('MaKhachHang')->references('MaKhachHang')->on('customers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_documents');
    }
};
