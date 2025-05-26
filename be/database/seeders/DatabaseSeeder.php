<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ToaNha;
use App\Models\Phong;
use App\Models\PhiDichVu;
use App\Models\KhachHang;
use App\Models\HopDong;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create ToaNha
        $toaNha1 = ToaNha::create([
            'ma_nha' => 'CH-001',
            'ten_nha' => 'Ben Hou',
            'dia_chi_nha' => '85 phố Viên',
            'xa_phuong' => 'Phường Cổ Nhuế 2',
            'quan_huyen' => 'Quận Bắc Từ Liêm',
            'tinh_thanh' => 'Thành phố Hà Nội',
            'trang_thai' => 'Hoạt động'
        ]);

        $toaNha2 = ToaNha::create([
            'ma_nha' => 'CH-002',
            'ten_nha' => 'Hom Tay',
            'dia_chi_nha' => '100 phố Phúc',
            'xa_phuong' => 'Phường Phúc Xá',
            'quan_huyen' => 'Quận Ba Đình',
            'tinh_thanh' => 'Thành phố Hà Nội',
            'trang_thai' => 'Không hoạt động'
        ]);

        // Create Phong
        $phong1 = Phong::create([
            'toa_nha_id' => $toaNha1->id,
            'ma_phong' => 'P.101',
            'ten_phong' => 'Phòng 101',
            'tang' => 'Tầng 1',
            'gia_thue' => 3000000,
            'dat_coc' => 3000000,
            'dien_tich' => 20,
            'so_khach_toi_da' => 3,
            'trang_thai' => 'Đang ở'
        ]);

        $phong2 = Phong::create([
            'toa_nha_id' => $toaNha1->id,
            'ma_phong' => 'P.201',
            'ten_phong' => 'Phòng 201',
            'tang' => 'Tầng 2',
            'gia_thue' => 2900000,
            'dat_coc' => 2900000,
            'dien_tich' => 20,
            'so_khach_toi_da' => 3,
            'trang_thai' => 'Đang ở'
        ]);

        $phong3 = Phong::create([
            'toa_nha_id' => $toaNha2->id,
            'ma_phong' => 'P.301',
            'ten_phong' => 'Phòng 301',
            'tang' => 'Tầng 3',
            'gia_thue' => 2800000,
            'dat_coc' => 2800000,
            'dien_tich' => 20,
            'so_khach_toi_da' => 3,
            'trang_thai' => 'Còn trống'
        ]);

        // Create PhiDichVu
        $dichVu1 = PhiDichVu::create([
            'toa_nha_id' => $toaNha1->id,
            'ma_dich_vu' => 'DV-05',
            'ten_dich_vu' => 'Gửi xe',
            'loai_dich_vu' => 'Tiền gửi xe',
            'don_gia' => 50000,
            'don_vi_tinh' => 'Xe'
        ]);

        $dichVu2 = PhiDichVu::create([
            'toa_nha_id' => $toaNha2->id,
            'ma_dich_vu' => 'DV-01',
            'ten_dich_vu' => 'Điện',
            'loai_dich_vu' => 'Tiền điện',
            'don_gia' => 2500,
            'don_vi_tinh' => 'Kwh'
        ]);

        $dichVu3 = PhiDichVu::create([
            'toa_nha_id' => $toaNha2->id,
            'ma_dich_vu' => 'DV-02',
            'ten_dich_vu' => 'Nước',
            'loai_dich_vu' => 'Tiền nước',
            'don_gia' => 9000,
            'don_vi_tinh' => 'm³'
        ]);

        $dichVu4 = PhiDichVu::create([
            'toa_nha_id' => $toaNha2->id,
            'ma_dich_vu' => 'DV-03',
            'ten_dich_vu' => 'Vệ sinh',
            'loai_dich_vu' => 'Tiền vệ sinh',
            'don_gia' => 30000,
            'don_vi_tinh' => 'Người'
        ]);

        $dichVu5 = PhiDichVu::create([
            'toa_nha_id' => $toaNha2->id,
            'ma_dich_vu' => 'DV-04',
            'ten_dich_vu' => 'Internet',
            'loai_dich_vu' => 'Tiền vệ sinh',
            'don_gia' => 100000,
            'don_vi_tinh' => 'Phòng'
        ]);

        // Create KhachHang
        $khachHang1 = KhachHang::create([
            'ma_khach_hang' => 'KH-001',
            'ho_ten' => 'Nguyễn Văn A',
            'so_dien_thoai' => '0123456789',
            'email' => 'nguyenvana@gmail.com',
            'cccd' => '0342000012345',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2001-11-11',
            'dia_chi_nha' => 'Thôn Ba',
            'xa_phuong' => 'Xã Yên Lâm',
            'quan_huyen' => 'Huyện Hàm Yên',
            'tinh_thanh' => 'Tỉnh Tuyên Quang'
        ]);

        $khachHang2 = KhachHang::create([
            'ma_khach_hang' => 'KH-002',
            'ho_ten' => 'Lê Thị B',
            'so_dien_thoai' => '0987654321',
            'email' => 'lethib@gmail.com',
            'cccd' => '0123450342000',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2001-12-22',
            'dia_chi_nha' => 'Thôn Trung',
            'xa_phuong' => 'Xã Sủng Tráng',
            'quan_huyen' => 'Huyện Yên Minh',
            'tinh_thanh' => 'Tỉnh Hà Giang'
        ]);

        $khachHang3 = KhachHang::create([
            'ma_khach_hang' => 'KH-003',
            'ho_ten' => 'Nguyễn Lê A',
            'so_dien_thoai' => '0123456789',
            'email' => 'nguyenlea@gmail.com',
            'cccd' => '0001234034205',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2000-11-11',
            'dia_chi_nha' => '55 phố Hoa',
            'xa_phuong' => 'Phường Tứ Liên',
            'quan_huyen' => 'Quận Tây Hồ',
            'tinh_thanh' => 'Thành phố Hà Nội'
        ]);

        // Create HopDong
        $hopDong1 = HopDong::create([
            'ma_hop_dong' => 'HD-001',
            'phong_id' => $phong1->id,
            'ngay_bat_dau' => '2025-01-01',
            'ngay_ket_thuc' => '2025-12-31',
            'tien_thue' => 3000000,
            'tien_coc' => 3000000,
            'chu_ky_thanh_toan' => '1 tháng',
            'ngay_tinh_tien' => '2025-01-01',
            'trang_thai' => 'Còn hạn'
        ]);

        $hopDong1->khachHangs()->attach([$khachHang1->id, $khachHang2->id]);
        $hopDong1->phiDichVus()->attach($dichVu1->id);

        $hopDong2 = HopDong::create([
            'ma_hop_dong' => 'HD-002',
            'phong_id' => $phong3->id,
            'ngay_bat_dau' => '2024-01-01',
            'ngay_ket_thuc' => '2025-12-31',
            'tien_thue' => 3000000,
            'tien_coc' => 3000000,
            'chu_ky_thanh_toan' => '1 tháng',
            'ngay_tinh_tien' => '2024-01-01',
            'trang_thai' => 'Hết hạn'
        ]);

        $hopDong2->khachHangs()->attach($khachHang3->id);
        $hopDong2->phiDichVus()->attach([
            $dichVu2->id => [
                'ma_cong_to' => 'CTD-001',
                'chi_so_dau' => 1,
                'ngay_tinh_phi' => '2024-01-01'
            ],
            $dichVu3->id => [
                'ma_cong_to' => 'CTN-001',
                'chi_so_dau' => 1,
                'ngay_tinh_phi' => '2024-01-01'
            ],
            $dichVu4->id => [
                'ngay_tinh_phi' => '2024-01-01'
            ],
            $dichVu5->id => [
                'ngay_tinh_phi' => '2024-01-01'
            ]
        ]);
    }
}
