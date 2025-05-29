import api from './config';

// Tòa nhà APIs
export const toaNhaService = {
    getAll: () => api.get('/toa-nha'),
    getById: (id) => api.get(`/toa-nha/${id}`),
    create: (data) => api.post('/toa-nha', data),
    update: (id, data) => api.put(`/toa-nha/${id}`, data),
    delete: (id) => api.delete(`/toa-nha/${id}`),
    search: (query) => api.get(`/toa-nha/search?q=${query}`),
    getPhongs: (id) => api.get(`/toa-nha/${id}/phong`)
};

// Phòng APIs
export const phongService = {
    getAll: () => api.get('/phong'),
    getById: (id) => api.get(`/phong/${id}`),
    create: (data) => api.post('/phong', data),
    update: (id, data) => api.put(`/phong/${id}`, data),
    delete: (id) => api.delete(`/phong/${id}`),
    search: (query) => api.get(`/phong/search?q=${query}`),
    getHopDong: (id) => api.get(`/phong/${id}/hop-dong`),
    getHoaDon: (id) => api.get(`/phong/${id}/hoa-don`)
};

// Dịch vụ APIs
export const phiDichVuService = {
    getAll: () => api.get('/phi-dich-vu'),
    getById: (id) => api.get(`/phi-dich-vu/${id}`),
    create: (data) => api.post('/phi-dich-vu', data),
    update: (id, data) => api.put(`/phi-dich-vu/${id}`, data),
    delete: (id) => api.delete(`/phi-dich-vu/${id}`),
    search: (query) => api.get(`/phi-dich-vu/search?q=${query}`)
};

// Khách hàng APIs
export const khachHangService = {
    getAll: () => api.get('/khach-hang'),
    getById: (id) => api.get(`/khach-hang/${id}`),
    create: (data) => api.post('/khach-hang', data),
    update: (id, data) => api.put(`/khach-hang/${id}`, data),
    delete: (id) => api.delete(`/khach-hang/${id}`),
    search: (query) => api.get(`/khach-hang/search?q=${query}`),
    getHopDongs: (id) => api.get(`/khach-hang/${id}/hop-dong`)
};

// Hợp đồng APIs
export const hopDongService = {
    getAll: () => api.get('/hop-dong'),
    getById: (id) => api.get(`/hop-dong/${id}`),
    create: (data) => api.post('/hop-dong', data),
    update: (id, data) => api.put(`/hop-dong/${id}`, data),
    delete: (id) => api.delete(`/hop-dong/${id}`),
    search: (query) => api.get(`/hop-dong/search?q=${query}`),
    getHoaDons: (id) => api.get(`/hop-dong/${id}/hoa-don`)
};

// Hóa đơn APIs
export const hoaDonService = {
    getAll: () => api.get('/hoa-don'),
    getById: (id) => api.get(`/hoa-don/${id}`),
    create: (data) => api.post('/hoa-don', data),
    update: (id, data) => api.put(`/hop-dong/${id}`, data),
    delete: (id) => api.delete(`/hoa-don/${id}`),
    search: (query) => api.get(`/hoa-don/search?q=${query}`),
    thanhToan: (id, data) => api.post(`/hoa-don/${id}/thanh-toan`, data)
};

// Giấy tờ APIs
export const giayToService = {
    getAll: () => api.get('/giay-to'),
    getById: (id) => api.get(`/giay-to/${id}`),
    create: (data) => api.post('/giay-to', data),
    update: (id, data) => api.put(`/hop-dong/${id}`, data),
    delete: (id) => api.delete(`/giay-to/${id}`),
    search: (query) => api.get(`/giay-to/search?q=${query}`),
    upload: (data) => api.post('/giay-to/upload', data),
    download: (id) => api.get(`/giay-to/${id}/download`)
};

// Báo cáo APIs
export const baoCaoService = {
    getAll: () => api.get('/bao-cao'),
    getById: (id) => api.get(`/bao-cao/${id}`),
    create: (data) => api.post('/bao-cao', data),
    update: (id, data) => api.put(`/bao-cao/${id}`, data),
    delete: (id) => api.delete(`/bao-cao/${id}`),
    search: (query) => api.get(`/bao-cao/search?q=${query}`),
    upload: (data) => api.post('/bao-cao/upload', data),
    download: (id) => api.get(`/bao-cao/${id}/download`)
};

// Chủ nhà APIs
export const chuNhaService = {
    getAll: () => api.get('/chu-nha'),
    getById: (id) => api.get(`/chu-nha/${id}`),
    create: (data) => api.post('/chu-nha', data),
    update: (id, data) => api.put(`/chu-nha/${id}`, data),
    delete: (id) => api.delete(`/chu-nha/${id}`),
    search: (query) => api.get(`/chu-nha/search?q=${query}`)
};

// Auth APIs
export const authService = {
    login: (credentials) => api.post('/login', credentials),
    register: (userData) => api.post('/register', userData),
    logout: () => api.post('/logout'),
    getUser: () => api.get('/user')
};