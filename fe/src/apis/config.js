import axios from 'axios';

const api = axios.create({
    baseURL: 'http://127.0.0.1:8000/api', // URL mặc định
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    },
    // Thêm timeout để tránh chờ quá lâu
    timeout: 10000,
    // Cho phép gửi cookie trong các yêu cầu cross-origin
    withCredentials: true
});

// Interceptor để thêm token vào header của mỗi request
api.interceptors.request.use(
    config => {
        const token = localStorage.getItem('token');
        if (token) {
            config.headers['Authorization'] = `Bearer ${token}`;
        }
        return config;
    },
    error => {
        return Promise.reject(error);
    }
);

// Interceptor để xử lý response và lỗi
api.interceptors.response.use(
    response => response,
    error => {
        // Xử lý lỗi 401 (Unauthorized)
        if (error.response && error.response.status === 401) {
            // Xóa token và chuyển hướng về trang đăng nhập
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/Login';
        }
        console.error('API Error:', error);
        return Promise.reject(error);
    }
);

export default api;