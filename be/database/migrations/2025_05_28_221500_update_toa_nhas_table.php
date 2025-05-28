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
        Schema::table('toa_nhas', function (Blueprint $table) {
            if (!Schema::hasColumn('toa_nhas', 'ma_nha')) {
                $table->string('ma_nha')->unique();
            }
            if (!Schema::hasColumn('toa_nhas', 'ten_nha')) {
                $table->string('ten_nha');
            }
            if (!Schema::hasColumn('toa_nhas', 'dia_chi_nha')) {
                $table->string('dia_chi_nha');
            }
            if (!Schema::hasColumn('toa_nhas', 'xa_phuong')) {
                $table->string('xa_phuong');
            }
            if (!Schema::hasColumn('toa_nhas', 'quan_huyen')) {
                $table->string('quan_huyen');
            }
            if (!Schema::hasColumn('toa_nhas', 'tinh_thanh')) {
                $table->string('tinh_thanh');
            }
            if (!Schema::hasColumn('toa_nhas', 'trang_thai')) {
                $table->string('trang_thai')->default('Không hoạt động');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('toa_nhas', function (Blueprint $table) {
            $table->dropColumn([
                'ma_nha',
                'ten_nha',
                'dia_chi_nha',
                'xa_phuong',
                'quan_huyen',
                'tinh_thanh',
                'trang_thai'
            ]);
        });
    }
};
