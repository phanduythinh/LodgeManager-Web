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
        Schema::create('hoa_dons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hop_dong_id')->constrained('hop_dongs')->onDelete('cascade');
            $table->date('ngay_lap');
            $table->date('ngay_thanh_toan')->nullable();
            $table->decimal('tong_tien', 15, 2);
            $table->string('trang_thai'); // Ví dụ: 'chua_thanh_toan', 'da_thanh_toan'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoa_dons');
    }
};
