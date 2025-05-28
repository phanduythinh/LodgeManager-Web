import { useState, useEffect } from 'react';
import api from '../apis/config';
import { Box, Typography, Button, CircularProgress, Alert } from '@mui/material';

const ApiTest = () => {
  const [status, setStatus] = useState('idle');
  const [message, setMessage] = useState('');
  const [error, setError] = useState('');

  // Hàm kiểm tra kết nối với backend
  const testApiConnection = async () => {
    try {
      setStatus('loading');
      setError('');
      
      // Thử gọi một API endpoint đơn giản từ backend
      // Thay đổi đường dẫn '/api-test' thành một endpoint thực tế trong backend của bạn
      const response = await api.get('/ping');
      
      setMessage(`Kết nối thành công! Phản hồi: ${JSON.stringify(response.data)}`);
      setStatus('success');
    } catch (err) {
      console.error('Lỗi kết nối API:', err);
      setError(`Lỗi kết nối: ${err.message}`);
      
      // Kiểm tra lỗi CORS
      if (err.message.includes('Network Error')) {
        setError('Lỗi kết nối mạng. Có thể do CORS không được cấu hình đúng hoặc backend không hoạt động.');
      }
      
      setStatus('error');
    }
  };

  return (
    <Box sx={{ p: 3, maxWidth: 600, mx: 'auto', mt: 4 }}>
      <Typography variant="h5" gutterBottom>
        Kiểm tra kết nối Frontend-Backend
      </Typography>
      
      <Button 
        variant="contained" 
        color="primary" 
        onClick={testApiConnection}
        disabled={status === 'loading'}
        sx={{ mb: 2 }}
      >
        {status === 'loading' ? (
          <>
            <CircularProgress size={24} color="inherit" sx={{ mr: 1 }} />
            Đang kiểm tra...
          </>
        ) : 'Kiểm tra kết nối API'}
      </Button>
      
      {status === 'success' && (
        <Alert severity="success" sx={{ mb: 2 }}>
          {message}
        </Alert>
      )}
      
      {status === 'error' && (
        <Alert severity="error" sx={{ mb: 2 }}>
          {error}
        </Alert>
      )}
      
      <Typography variant="body2" color="text.secondary" sx={{ mt: 2 }}>
        <strong>Thông tin cấu hình:</strong>
        <br />
        Frontend URL: http://localhost:5173
        <br />
        Backend URL: {api.defaults.baseURL}
      </Typography>
    </Box>
  );
};

export default ApiTest;
