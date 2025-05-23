export const ToaNhaData = [
  {
    MaNha: 'CH-001',
    TenNha: 'Ben Hou',
    DiaChiNha: '85 phố Viên',
    QuanHuyen: 'Bắc Từ Liêm',
    TinhThanh: 'Hà Nội',
    XaPhuong: 'Cổ Nhuế 2',
    TrangThai: 'Hoạt động',
    Phongs: [
      {
        MaPhong: 'P.101',
        TenPhong: 'Phòng 101',
        Tang: 'Tầng 1',
        GiaThue: '3000000',
        DatCoc: '3000000',
        DienTich: '20',
        SoKhachToiDa: '3',
        TrangThai: 'Đang ở'
      },
      {
        MaPhong: 'P.201',
        TenPhong: 'Phòng 201',
        Tang: 'Tầng 2',
        GiaThue: '2900000',
        DatCoc: '2900000',
        DienTich: '20',
        SoKhachToiDa: '3',
        TrangThai: 'Đang ở'
      }
    ],
    PhiDichVus: [] // chưa có dịch vụ
  },
  {
    MaNha: 'CH-002',
    TenNha: 'Hom Tay',
    DiaChiNha: '100 phố Viên',
    QuanHuyen: 'Bắc Từ Liêm',
    TinhThanh: 'Hà Nội',
    XaPhuong: 'Cổ Nhuế 2',
    TrangThai: 'Không hoạt động',
    Phongs: [
      {
        MaPhong: 'P.301',
        TenPhong: 'Phòng 301',
        Tang: 'Tầng 3',
        GiaThue: '2800000',
        DatCoc: '2800000',
        DienTich: '20',
        SoKhachToiDa: '3',
        TrangThai: 'Còn trống'
      }
    ],
    PhiDichVus: [
      {
        MaDichVu: 'DV-01',
        TenDichVu: 'Điện',
        LoaiDichVu: 'Tiền điện',
        DonGia: '2500',
        DonViTinh: 'đ/Kwh'
      },
      {
        MaDichVu: 'DV-02',
        TenDichVu: 'Nước',
        LoaiDichVu: 'Tiền nước',
        DonGia: '9000',
        DonViTinh: 'm³'
      },
      {
        MaDichVu: 'DV-03',
        TenDichVu: 'Vệ sinh',
        LoaiDichVu: 'Tiền vệ sinh',
        DonGia: '30000',
        DonViTinh: 'đ/người'
      },
      {
        MaDichVu: 'DV-04',
        TenDichVu: 'Internet',
        LoaiDichVu: 'Tiền vệ sinh',
        DonGia: '100000',
        DonViTinh: 'đ/phòng'
      }
    ]
  }
];

export const KhachHangs = [
  {
    MaKhachHang: 'KH-001',
    HoTen: 'Nguyễn Văn A',
    SoDienThoai: '0123456789',
    Email: 'nguyenvana@gmail.com',
    CCCD: '0342000012345',
    GioiTinh: 'Nam',
    NgaySinh: '11/11/1111',
    DiaChiNha: 'Thôn Ba',
    XaPhuong: 'Song Lãng',
    QuanHuyen: 'Vũ Thư',
    TinhThanh: 'Thái Bình',
  },
  {
    MaKhachHang: 'KH-002',
    HoTen: 'Lê Thị B',
    SoDienThoai: '0987654321',
    Email: 'lethib@gmail.com',
    CCCD: '0123450342000',
    GioiTinh: 'Nữ',
    NgaySinh: '22/12/2001',
    DiaChiNha: 'Thôn Trung',
    XaPhuong: 'Song Lãng',
    QuanHuyen: 'Vũ Thư',
    TinhThanh: 'Thái Bình'
  }
]

export const HopDongs = [
  {
    MaHopDong: 'HD-001',
    MaNhaId: 'CH-002',
    MaPhong: 'Phong 301',
    NgayBatDau: '01/01/2025',
    NgayKetThuc: '31/12/2025',
    TienThue: '3000000',
    TienCoc: '3000000',
    ChuKyThanhToan: '1 tháng',
    NgayTinhTien: '01',
    TrangThai: 'Còn hạn',
    KhachHangs: [
      {
        MaKhachHang: 'KH-001',
        HoTen: 'Nguyễn Văn A',
        SoDienThoai: '0123456789',
        Email: 'nguyenvana@gmail.com',
        CCCD: '0342000012345',
        GioiTinh: 'Nam',
        NgaySinh: '11/11/1111',
        DiaChiNha: 'Thôn Ba',
        XaPhuong: 'Song Lãng',
        QuanHuyen: 'Vũ Thư',
        TinhThanh: 'Thái Bình'
      },
      {
        MaKhachHang: 'KH-002',
        HoTen: 'Lê Thị B',
        SoDienThoai: '0987654321',
        Email: 'lethib@gmail.com',
        CCCD: '0123450342000',
        GioiTinh: 'Nữ',
        NgaySinh: '22/12/2001',
        DiaChiNha: 'Thôn Trung',
        XaPhuong: 'Song Lãng',
        QuanHuyen: 'Vũ Thư',
        TinhThanh: 'Thái Bình'
      }
    ],
    DichVus: [
      {
        MaDichVu: 'DV-01',
        TenDichVu: 'Điện',
        LoaiDichVu: 'Tiền điện',
        DonGia: '2500',
        DonViTinh: 'đ/Kwh'
      },
      {
        MaDichVu: 'DV-02',
        TenDichVu: 'Nước',
        LoaiDichVu: 'Tiền nước',
        DonGia: '9000',
        DonViTinh: 'm³'
      },
      {
        MaDichVu: 'DV-03',
        TenDichVu: 'Vệ sinh',
        LoaiDichVu: 'Tiền vệ sinh',
        DonGia: '30000',
        DonViTinh: 'đ/người'
      },
      {
        MaDichVu: 'DV-04',
        TenDichVu: 'Internet',
        LoaiDichVu: 'Tiền vệ sinh',
        DonGia: '100000',
        DonViTinh: 'đ/phòng'
      }
    ]
  }
];
