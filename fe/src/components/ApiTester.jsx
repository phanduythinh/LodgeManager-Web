import { useState, useEffect } from 'react';
import { Box, Button, Typography, Paper, CircularProgress, Alert, TextField } from '@mui/material';
import { toaNhaService } from '../apis/services';

const ApiTester = () => {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const [newToaNha, setNewToaNha] = useState({
    MaNha: '',
    TenNha: '',
    DiaChiNha: '',
    TinhThanh: 'Hà Nội',
    QuanHuyen: 'Cầu Giấy',
    XaPhuong: 'Dịch Vọng',
    TrangThai: 'Hoạt động'
  });

  // Hàm tải dữ liệu từ API
  const fetchData = async () => {
    try {
      setLoading(true);
      setError('');
      const response = await toaNhaService.getAll();
      setData(response.data);
      setSuccess('Tải dữ liệu thành công!');
    } catch (err) {
      console.error('Lỗi khi tải dữ liệu:', err);
      setError(`Lỗi khi tải dữ liệu: ${err.message}`);
    } finally {
      setLoading(false);
    }
  };

  // Hàm thêm tòa nhà mới
  const addToaNha = async () => {
    try {
      setLoading(true);
      setError('');
      const response = await toaNhaService.create(newToaNha);
      setSuccess('Thêm tòa nhà thành công!');
      // Tải lại dữ liệu sau khi thêm
      fetchData();
    } catch (err) {
      console.error('Lỗi khi thêm tòa nhà:', err);
      setError(`Lỗi khi thêm tòa nhà: ${err.message}`);
    } finally {
      setLoading(false);
    }
  };

  // Hàm xóa tòa nhà
  const deleteToaNha = async (id) => {
    try {
      setLoading(true);
      setError('');
      await toaNhaService.delete(id);
      setSuccess('Xóa tòa nhà thành công!');
      // Tải lại dữ liệu sau khi xóa
      fetchData();
    } catch (err) {
      console.error('Lỗi khi xóa tòa nhà:', err);
      setError(`Lỗi khi xóa tòa nhà: ${err.message}`);
    } finally {
      setLoading(false);
    }
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setNewToaNha(prev => ({ ...prev, [name]: value }));
  };

  // Tải dữ liệu khi component được mount
  useEffect(() => {
    fetchData();
  }, []);

  return (
    <Box sx={{ p: 3, maxWidth: 800, mx: 'auto' }}>
      <Typography variant="h4" gutterBottom>
        Kiểm tra API
      </Typography>

      {error && (
        <Alert severity="error" sx={{ mb: 2 }}>
          {error}
        </Alert>
      )}

      {success && (
        <Alert severity="success" sx={{ mb: 2 }}>
          {success}
        </Alert>
      )}

      <Box sx={{ mb: 4 }}>
        <Typography variant="h6" gutterBottom>
          Thêm tòa nhà mới
        </Typography>
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2, mb: 2 }}>
          <TextField
            label="Mã nhà"
            name="MaNha"
            value={newToaNha.MaNha}
            onChange={handleChange}
          />
          <TextField
            label="Tên nhà"
            name="TenNha"
            value={newToaNha.TenNha}
            onChange={handleChange}
          />
          <TextField
            label="Địa chỉ nhà"
            name="DiaChiNha"
            value={newToaNha.DiaChiNha}
            onChange={handleChange}
          />
        </Box>
        <Button 
          variant="contained" 
          onClick={addToaNha}
          disabled={loading}
        >
          {loading ? 'Đang xử lý...' : 'Thêm tòa nhà'}
        </Button>
      </Box>

      <Box sx={{ mb: 2, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <Typography variant="h6">
          Danh sách tòa nhà
        </Typography>
        <Button 
          variant="outlined" 
          onClick={fetchData}
          disabled={loading}
        >
          {loading ? <CircularProgress size={24} /> : 'Tải lại dữ liệu'}
        </Button>
      </Box>

      {loading ? (
        <Box sx={{ display: 'flex', justifyContent: 'center', my: 4 }}>
          <CircularProgress />
        </Box>
      ) : (
        <Box>
          {data.length === 0 ? (
            <Alert severity="info">Không có dữ liệu</Alert>
          ) : (
            data.map((item) => (
              <Paper key={item.id || item.MaNha} sx={{ p: 2, mb: 2 }}>
                <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                  <Box>
                    <Typography variant="h6">{item.TenNha}</Typography>
                    <Typography variant="body2">Mã nhà: {item.MaNha}</Typography>
                    <Typography variant="body2">
                      Địa chỉ: {item.DiaChiNha}, {item.XaPhuong}, {item.QuanHuyen}, {item.TinhThanh}
                    </Typography>
                    <Typography variant="body2">
                      Trạng thái: <span style={{ color: item.TrangThai === 'Hoạt động' ? 'green' : 'red' }}>
                        {item.TrangThai}
                      </span>
                    </Typography>
                    <Typography variant="body2">
                      Số phòng: {Array.isArray(item.Phongs) ? item.Phongs.length : 0}
                    </Typography>
                  </Box>
                  <Button 
                    variant="contained" 
                    color="error"
                    onClick={() => deleteToaNha(item.id || item.MaNha)}
                    disabled={loading}
                  >
                    Xóa
                  </Button>
                </Box>
              </Paper>
            ))
          )}
        </Box>
      )}
    </Box>
  );
};

export default ApiTester;
