// Các hàm tiện ích để xử lý và định dạng dữ liệu

/**
 * Xử lý dữ liệu CMND/CCCD từ nhiều nguồn khác nhau
 * @param {Object} data - Dữ liệu khách hàng
 * @returns {string} - CMND/CCCD đã được xử lý
 */
export const formatCCCD = (data) => {
  if (!data) return '';
  return data.CCCD || data.CMND_CCCD || data.cccd || '';
};

/**
 * Tạo địa chỉ đầy đủ từ các thành phần
 * @param {Object} data - Dữ liệu địa chỉ
 * @returns {string} - Địa chỉ đầy đủ
 */
export const formatAddress = (data) => {
  if (!data) return '';
  
  const parts = [];
  
  // Thêm các thành phần địa chỉ theo thứ tự
  if (data.DiaChiNha || data.dia_chi_nha) parts.push(data.DiaChiNha || data.dia_chi_nha);
  if (data.XaPhuong || data.xa_phuong) parts.push(data.XaPhuong || data.xa_phuong);
  if (data.QuanHuyen || data.quan_huyen) parts.push(data.QuanHuyen || data.quan_huyen);
  if (data.TinhThanh || data.tinh_thanh) parts.push(data.TinhThanh || data.tinh_thanh);
  
  // Lọc bỏ các giá trị rỗng và nối lại bằng dấu phẩy
  return parts.filter(Boolean).join(', ');
};

/**
 * Chuyển đổi dữ liệu từ backend sang định dạng frontend
 * @param {Object} data - Dữ liệu từ backend
 * @returns {Object} - Dữ liệu đã được chuẩn hóa cho frontend
 */
export const normalizeCustomerData = (data) => {
  if (!data) return {};
  
  return {
    ...data,
    CCCD: formatCCCD(data),
    DiaChiNha: data.DiaChiNha || data.dia_chi_nha || '',
    XaPhuong: data.XaPhuong || data.xa_phuong || '',
    QuanHuyen: data.QuanHuyen || data.quan_huyen || '',
    TinhThanh: data.TinhThanh || data.tinh_thanh || '',
    GioiTinh: data.GioiTinh || data.gioi_tinh || 'Nam',
    NgaySinh: data.NgaySinh || data.ngay_sinh || null
  };
};
