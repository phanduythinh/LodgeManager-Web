export const ToaNhaData = [
  {
    MaNha: 'CH-001',
    TenNha: 'Ben Hou',
    DiaChiNha: '85 phố Viên',
    XaPhuong: 'Phường Cổ Nhuế 2',
    QuanHuyen: 'Quận Bắc Từ Liêm',
    TinhThanh: 'Thành phố Hà Nội',
    TrangThai: 'Hoạt động',
    Phongs: [
      { MaPhong: 'P.101', TenPhong: 'Phòng 101', Tang: 'Tầng 1', GiaThue: '3000000', DatCoc: '3000000', DienTich: '20', SoKhachToiDa: '3', TrangThai: 'Đang ở' },
      { MaPhong: 'P.201', TenPhong: 'Phòng 201', Tang: 'Tầng 2', GiaThue: '2900000', DatCoc: '2900000', DienTich: '20', SoKhachToiDa: '3', TrangThai: 'Đang ở' }
    ],
    PhiDichVus: [
      { MaDichVu: 'DV-05', TenDichVu: 'Gửi xe', LoaiDichVu: 'Tiền gửi xe', DonGia: '50000', DonViTinh: 'Xe' }
    ]
  },
  {
    MaNha: 'CH-002',
    TenNha: 'Hom Tay',
    DiaChiNha: '100 phố Phúc',
    XaPhuong: 'Phường Phúc Xá',
    QuanHuyen: 'Quận Ba Đình',
    TinhThanh: 'Thành phố Hà Nội',
    TrangThai: 'Không hoạt động',
    Phongs: [
      { MaPhong: 'P.301', TenPhong: 'Phòng 301', Tang: 'Tầng 3', GiaThue: '2800000', DatCoc: '2800000', DienTich: '20', SoKhachToiDa: '3', TrangThai: 'Còn trống' }
    ],
    PhiDichVus: [
      { MaDichVu: 'DV-01', TenDichVu: 'Điện', LoaiDichVu: 'Tiền điện', DonGia: '2500', DonViTinh: 'Kwh' },
      { MaDichVu: 'DV-02', TenDichVu: 'Nước', LoaiDichVu: 'Tiền nước', DonGia: '9000', DonViTinh: 'm³' },
      { MaDichVu: 'DV-03', TenDichVu: 'Vệ sinh', LoaiDichVu: 'Tiền vệ sinh', DonGia: '30000', DonViTinh: 'Người' },
      { MaDichVu: 'DV-04', TenDichVu: 'Internet', LoaiDichVu: 'Tiền vệ sinh', DonGia: '100000', DonViTinh: 'Phòng' }
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
    NgaySinh: '11/11/2001',
    DiaChiNha: 'Thôn Ba',
    XaPhuong: 'Xã Yên Lâm',
    QuanHuyen: 'Huyện Hàm Yên',
    TinhThanh: 'Tỉnh Tuyên Quang',
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
    XaPhuong: 'Xã Sủng Tráng',
    QuanHuyen: 'Huyện Yên Minh',
    TinhThanh: 'Tỉnh Hà Giang'
  },
  {
    MaKhachHang: 'KH-003',
    HoTen: 'Nguyễn Lê A',
    SoDienThoai: '0123456789',
    Email: 'nguyenlea@gmail.com',
    CCCD: '0001234034205',
    GioiTinh: 'Nữ',
    NgaySinh: '11/11/2000',
    DiaChiNha: '55 phố Hoa',
    XaPhuong: 'Phường Tứ Liên',
    QuanHuyen: 'Quận Tây Hồ',
    TinhThanh: 'Thành phố Hà Nội'
  },
]

export const HopDongs = [
  {
    MaHopDong: 'HD-001',
    TenNha: 'Ben Hou',
    TenPhong: 'Phòng 101',
    NgayBatDau: '01/01/2025',
    NgayKetThuc: '31/12/2025',
    TienThue: '3000000',
    TienCoc: '3000000',
    ChuKyThanhToan: '1 tháng',
    NgayTinhTien: '01/01/2025',
    TrangThai: 'Còn hạn',
    KhachHangs: [
      {
        MaKhachHang: 'KH-001',
        HoTen: 'Nguyễn Văn A',
        SoDienThoai: '0123456789',
        Email: 'nguyenvana@gmail.com',
        CCCD: '0342000012345',
        GioiTinh: 'Nam',
        NgaySinh: '11/11/2001',
        DiaChiNha: 'Thôn Ba',
        XaPhuong: 'Xã Yên Lâm',
        QuanHuyen: 'Huyện Hàm Yên',
        TinhThanh: 'Tỉnh Tuyên Quang',
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
        XaPhuong: 'Xã Sủng Tráng',
        QuanHuyen: 'Huyện Yên Minh',
        TinhThanh: 'Tỉnh Hà Giang'
      }
    ],
    DichVus: [
      {
        MaDichVu: 'DV-05',
        TenDichVu: 'Gửi xe',
        MaCongTo: '',
        ChiSoDau: '',
        NgayTinhPhi: '',
        LoaiDichVu: 'Tiền gửi xe',
        DonGia: '50000',
        DonViTinh: 'Xe'
      }
    ]
  },
  {
    MaHopDong: 'HD-002',
    TenNha: 'Hom Tay',
    TenPhong: 'Phòng 301',
    NgayBatDau: '01/01/2024',
    NgayKetThuc: '31/12/2025',
    TienThue: '3000000',
    TienCoc: '3000000',
    ChuKyThanhToan: '1 tháng',
    NgayTinhTien: '01/01/2024',
    TrangThai: 'Hết hạn',
    KhachHangs: [
      {
        MaKhachHang: 'KH-003',
        HoTen: 'Nguyễn Lê A',
        SoDienThoai: '0123456789',
        Email: 'nguyenlea@gmail.com',
        CCCD: '0001234034205',
        GioiTinh: 'Nữ',
        NgaySinh: '11/11/2000',
        DiaChiNha: '55 phố Hoa',
        XaPhuong: 'Phường Tứ Liên',
        QuanHuyen: 'Quận Tây Hồ',
        TinhThanh: 'Thành phố Hà Nội'
      }
    ],
    DichVus: [
      {
        MaDichVu: 'DV-01',
        TenDichVu: 'Điện',
        MaCongTo: 'CTD-001',
        ChiSoDau: '1',
        NgayTinhPhi: '01/01/2024',
        LoaiDichVu: 'Tiền điện',
        DonGia: '2500',
        DonViTinh: 'đ/Kwh'
      },
      {
        MaDichVu: 'DV-02',
        TenDichVu: 'Nước',
        MaCongTo: 'CTN-001',
        ChiSoDau: '1',
        NgayTinhPhi: '01/01/2024',
        LoaiDichVu: 'Tiền nước',
        DonGia: '9000',
        DonViTinh: 'm³'
      },
      {
        MaDichVu: 'DV-03',
        TenDichVu: 'Vệ sinh',
        MaCongTo: '',
        ChiSoDau: '',
        NgayTinhPhi: '01/01/2024',
        LoaiDichVu: 'Tiền vệ sinh',
        DonGia: '30000',
        DonViTinh: 'đ/người'
      },
      {
        MaDichVu: 'DV-04',
        TenDichVu: 'Internet',
        MaCongTo: '',
        ChiSoDau: '',
        NgayTinhPhi: '01/01/2024',
        LoaiDichVu: 'Tiền vệ sinh',
        DonGia: '100000',
        DonViTinh: 'đ/phòng'
      }
    ]
  }
];
