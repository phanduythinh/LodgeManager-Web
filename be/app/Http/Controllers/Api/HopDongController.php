<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HopDong;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HopDongController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $hopDongs = HopDong::with(['phong.toaNha', 'khachHangs', 'phiDichVus'])->get();
            
            $formattedHopDongs = $hopDongs->map(function ($hopDong) {
                return $this->formatHopDong($hopDong);
            });
            
            return response()->json($formattedHopDongs);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy danh sách hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        // 1. Normalize data before validation
        $data = $request->all();
        $data['NgayBatDau'] = $this->normalizeDateToYMD($request->input('NgayBatDau'));
        $data['NgayKetThuc'] = $this->normalizeDateToYMD($request->input('NgayKetThuc'));
        $data['NgayTinhTien'] = $this->normalizeDateToYMD($request->input('NgayTinhTien'));

        // 2. Validate the normalized data
        $validator = Validator::make($data, [
            'MaHopDong' => 'required|string|unique:hop_dongs,ma_hop_dong|max:255',
            'MaPhongId' => 'required|integer|exists:phongs,id',
            'NgayBatDau' => 'nullable|date_format:Y-m-d',
            'NgayKetThuc' => 'nullable|date_format:Y-m-d|after_or_equal:NgayBatDau',
            'TienThue' => 'nullable|numeric|min:0',
            'TienCoc' => 'nullable|numeric|min:0',
            'ChuKyThanhToan' => 'nullable|integer|min:1',
            'NgayTinhTien' => 'nullable|date_format:Y-m-d',
            'TrangThai' => 'nullable|string',
            'KhachHangs' => 'required|array|min:1',
            'KhachHangs.*.id' => 'required|integer|exists:khach_hangs,id',
            'DichVus' => 'nullable|array',
            'DichVus.*.id' => 'required_with:DichVus|integer|exists:phi_dich_vus,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 3. Create the contract using validated data
            $hopDong = HopDong::create([
                'ma_hop_dong' => $data['MaHopDong'],
                'phong_id' => $data['MaPhongId'],
                'ngay_bat_dau' => $data['NgayBatDau'],
                'ngay_ket_thuc' => $data['NgayKetThuc'],
                'tien_thue' => $data['TienThue'] ?? 0,
                'tien_coc' => $data['TienCoc'] ?? 0,
                'chu_ky_thanh_toan' => $data['ChuKyThanhToan'] ?? 1,
                'ngay_tinh_tien' => $data['NgayTinhTien'],
                'trang_thai' => $data['TrangThai'] ?? 'Còn hạn',
            ]);

            // 4. Attach customers
            $khachHangIds = collect($data['KhachHangs'])->pluck('id')->all();
            $hopDong->khachHangs()->attach($khachHangIds);

            // 5. Prepare and attach services
            if (!empty($data['DichVus']) && is_array($data['DichVus'])) {
                $dichVuData = [];
                foreach ($data['DichVus'] as $dichVu) {
                    if (!empty($dichVu['id'])) {
                        $dichVuData[$dichVu['id']] = [
                            'ma_cong_to' => $dichVu['MaCongTo'] ?? null,
                            'chi_so_dau' => $dichVu['ChiSoDau'] ?? null,
                            'ngay_tinh_phi' => $this->normalizeDateToYMD($dichVu['NgayTinhPhi'] ?? null),
                        ];
                    }
                }
                if (!empty($dichVuData)) {
                    $hopDong->phiDichVus()->attach($dichVuData);
                }
            }

            DB::commit();

            $hopDong->load(['phong.toaNha', 'khachHangs', 'phiDichVus']);
            return response()->json([
                'status' => 'success',
                'message' => 'Hợp đồng đã được tạo thành công.',
                'data' => $this->formatHopDong($hopDong)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo hợp đồng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi tạo hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(HopDong $hopDong): JsonResponse
    {
        $hopDong->load(['phong.toaNha', 'khachHangs', 'phiDichVus']);
        return response()->json($hopDong);
    }

    public function update(Request $request, HopDong $hopDong): JsonResponse
    {
        // 1. Normalize data before validation
        $data = $request->all();
        $data['NgayBatDau'] = $this->normalizeDateToYMD($request->input('NgayBatDau'));
        $data['NgayKetThuc'] = $this->normalizeDateToYMD($request->input('NgayKetThuc'));
        $data['NgayTinhTien'] = $this->normalizeDateToYMD($request->input('NgayTinhTien'));

        // 2. Validate the normalized data
        $validator = Validator::make($data, [
            'MaHopDong' => 'required|string|max:255|unique:hop_dongs,ma_hop_dong,' . $hopDong->id,
            'MaPhongId' => 'required|integer|exists:phongs,id',
            'NgayBatDau' => 'nullable|date_format:Y-m-d',
            'NgayKetThuc' => 'nullable|date_format:Y-m-d|after_or_equal:NgayBatDau',
            'TienThue' => 'nullable|numeric|min:0',
            'TienCoc' => 'nullable|numeric|min:0',
            'ChuKyThanhToan' => 'nullable|integer|min:1',
            'NgayTinhTien' => 'nullable|date_format:Y-m-d',
            'TrangThai' => 'nullable|string',
            'KhachHangs' => 'required|array|min:1',
            'KhachHangs.*.id' => 'required|integer|exists:khach_hangs,id',
            'DichVus' => 'nullable|array',
            'DichVus.*.id' => 'required_with:DichVus|integer|exists:phi_dich_vus,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 3. Update the contract using validated data
            $hopDong->update([
                'ma_hop_dong' => $data['MaHopDong'],
                'phong_id' => $data['MaPhongId'],
                'ngay_bat_dau' => $data['NgayBatDau'],
                'ngay_ket_thuc' => $data['NgayKetThuc'],
                'tien_thue' => $data['TienThue'] ?? 0,
                'tien_coc' => $data['TienCoc'] ?? 0,
                'chu_ky_thanh_toan' => $data['ChuKyThanhToan'] ?? 1,
                'ngay_tinh_tien' => $data['NgayTinhTien'],
                'trang_thai' => $data['TrangThai'] ?? 'Còn hạn',
            ]);

            // 4. Sync customers
            $khachHangIds = collect($data['KhachHangs'])->pluck('id')->all();
            $hopDong->khachHangs()->sync($khachHangIds);

            // 5. Prepare and sync services
            $dichVuData = [];
            if (!empty($data['DichVus']) && is_array($data['DichVus'])) {
                foreach ($data['DichVus'] as $dichVu) {
                    if (!empty($dichVu['id'])) {
                        $dichVuData[$dichVu['id']] = [
                            'ma_cong_to' => $dichVu['MaCongTo'] ?? null,
                            'chi_so_dau' => $dichVu['ChiSoDau'] ?? null,
                            'ngay_tinh_phi' => $this->normalizeDateToYMD($dichVu['NgayTinhPhi'] ?? null),
                        ];
                    }
                }
            }
            $hopDong->phiDichVus()->sync($dichVuData);

            DB::commit();

            // 6. Return the updated and formatted contract
            $hopDong->load(['phong.toaNha', 'khachHangs', 'phiDichVus']);
            return response()->json($this->formatHopDong($hopDong));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật hợp đồng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi cập nhật hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $hopDong = HopDong::with('hoaDons')->findOrFail($id);

            // Kiểm tra xem hợp đồng đã có hóa đơn chưa
            if ($hopDong->hoaDons()->exists()) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể xóa hợp đồng đã có hóa đơn. Vui lòng xóa các hóa đơn liên quan trước.'
                ], 400); // 400 Bad Request
            }

            // Xóa các bản ghi liên quan trong bảng hop_dong_khach_hang
            $hopDong->khachHangs()->detach();

            // Xóa các bản ghi liên quan trong bảng hop_dong_phi_dich_vu
            $hopDong->phiDichVus()->detach();

            $hopDong->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Xóa hợp đồng thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa hợp đồng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi xóa hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Hàm hỗ trợ định dạng ngày tháng
     * 
     * @param string|null $date Ngày tháng cần định dạng
     * @return string|null Ngày tháng đã định dạng hoặc null
     */
    private function formatHopDong(HopDong $hopDong): array
    {
        return [
            'id' => $hopDong->id,
            'MaHopDong' => $hopDong->ma_hop_dong,
            'MaPhongId' => $hopDong->phong_id,
            'TenPhong' => $hopDong->phong ? $hopDong->phong->ten_phong : '',
            'MaNhaId' => $hopDong->phong && $hopDong->phong->toaNha ? $hopDong->phong->toaNha->ma_nha : null,
            'TenNha' => $hopDong->phong && $hopDong->phong->toaNha ? $hopDong->phong->toaNha->ten_nha : '',
            'NgayBatDau' => $hopDong->ngay_bat_dau,
            'NgayKetThuc' => $hopDong->ngay_ket_thuc,
            'TienThue' => $hopDong->tien_thue,
            'TienCoc' => $hopDong->tien_coc,
            'ChuKyThanhToan' => $hopDong->chu_ky_thanh_toan,
            'NgayTinhTien' => $hopDong->ngay_tinh_tien,
            'TrangThai' => $hopDong->trang_thai,
            'KhachHangs' => $hopDong->khachHangs ? $hopDong->khachHangs->map(function ($khachHang) {
                return [
                    'id' => $khachHang->id,
                    'MaKhachHang' => $khachHang->ma_khach_hang,
                    'HoTen' => $khachHang->ho_ten,
                    'NgaySinh' => $khachHang->ngay_sinh,
                    'GioiTinh' => $khachHang->gioi_tinh ? $khachHang->gioi_tinh : 'Nam',
                    'SoDienThoai' => $khachHang->so_dien_thoai,
                    'Email' => $khachHang->email,
                    'CCCD' => $khachHang->cmnd_cccd ? $khachHang->cmnd_cccd : '',
                    'NgayCap' => $khachHang->ngay_cap ? $khachHang->ngay_cap : '',
                    'NoiCap' => $khachHang->noi_cap ? $khachHang->noi_cap : '',
                    'DiaChi' => $khachHang->dia_chi ? $khachHang->dia_chi : '',
                    'TrangThai' => $khachHang->trang_thai ? $khachHang->trang_thai : 'Hoạt động'
                ];
            })->toArray() : [],
            'DichVus' => $hopDong->phiDichVus ? $hopDong->phiDichVus->map(function ($dichVu) use ($hopDong) {
                $pivot = $dichVu->pivot;
                return [
                    'id' => $dichVu->id,
                    'MaDichVu' => $dichVu->ma_dich_vu,
                    'TenDichVu' => $dichVu->ten_dich_vu,
                    'DonGia' => $dichVu->don_gia,
                    'DonViTinh' => $dichVu->don_vi_tinh,
                    'MaCongTo' => $pivot ? $pivot->ma_cong_to : null,
                    'ChiSoDau' => $pivot ? $pivot->chi_so_dau : null,
                    'NgayTinhPhi' => $pivot ? $pivot->ngay_tinh_phi : null
                ];
            })->toArray() : []
        ];
    }

    private function normalizeDateToYMD($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Attempt to create a DateTime object from various formats
            // This handles ISO 8601 (like 2023-10-27T17:00:00.000Z) and others
            $dt = new \DateTime($date);
            return $dt->format('Y-m-d');
        } catch (\Exception $e) {
            // If the above fails, try parsing specific formats like d/m/Y
            try {
                // Handle d/m/Y format
                if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $date)) {
                    $dt = \DateTime::createFromFormat('d/m/Y', $date);
                    if ($dt !== false) {
                        return $dt->format('Y-m-d');
                    }
                }
                // Handle Y-m-d format (already correct)
                 if (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $date)) {
                    return $date;
                }
            } catch (\Exception $inner_e) {
                Log::warning("Could not parse date: " . $date . ". Error: " . $inner_e->getMessage());
                return null; // Return null if all parsing fails
            }
        }
        
        Log::warning("Could not parse date: " . $date);
        return null; // Return null if parsing fails
    }
}
