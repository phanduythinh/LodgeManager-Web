import { useState, useEffect } from 'react'
import {
  Box,
  Typography,
  Paper,
  Alert,
  CircularProgress,
  Button,
  Grid,
  Card,
  CardContent,
  Divider
} from '@mui/material'
import axios from 'axios'
import { toaNhaService, phongService, phiDichVuService, khachHangService, hopDongService } from '../apis/services'

function ApiConnectionTest() {
  const [apiStatus, setApiStatus] = useState({
    ping: { status: 'idle', message: '' },
    toaNha: { status: 'idle', message: '', count: 0 },
    phong: { status: 'idle', message: '', count: 0 },
    phiDichVu: { status: 'idle', message: '', count: 0 },
    khachHang: { status: 'idle', message: '', count: 0 },
    hopDong: { status: 'idle', message: '', count: 0 }
  })
  const [loading, setLoading] = useState(false)

  const testAllConnections = async () => {
    setLoading(true)
    
    // Reset all statuses
    setApiStatus({
      ping: { status: 'loading', message: 'Đang kiểm tra...' },
      toaNha: { status: 'loading', message: 'Đang kiểm tra...' },
      phong: { status: 'loading', message: 'Đang kiểm tra...' },
      phiDichVu: { status: 'loading', message: 'Đang kiểm tra...' },
      khachHang: { status: 'loading', message: 'Đang kiểm tra...' },
      hopDong: { status: 'loading', message: 'Đang kiểm tra...' }
    })
    
    // Test ping endpoint
    try {
      const pingResponse = await axios.get('http://127.0.0.1:8000/api/ping', {
        withCredentials: true
      })
      setApiStatus(prev => ({
        ...prev,
        ping: { 
          status: 'success', 
          message: pingResponse.data.message,
          timestamp: pingResponse.data.timestamp
        }
      }))
    } catch (error) {
      setApiStatus(prev => ({
        ...prev,
        ping: { 
          status: 'error', 
          message: error.message || 'Không thể kết nối đến máy chủ'
        }
      }))
    }
    
    // Test toaNha endpoint
    try {
      const toaNhaResponse = await toaNhaService.getAll()
      setApiStatus(prev => ({
        ...prev,
        toaNha: { 
          status: 'success', 
          message: 'Kết nối thành công',
          count: toaNhaResponse.data.length || 0
        }
      }))
    } catch (error) {
      setApiStatus(prev => ({
        ...prev,
        toaNha: { 
          status: 'error', 
          message: error.message || 'Không thể kết nối đến API Tòa nhà'
        }
      }))
    }
    
    // Test phong endpoint
    try {
      const phongResponse = await phongService.getAll()
      setApiStatus(prev => ({
        ...prev,
        phong: { 
          status: 'success', 
          message: 'Kết nối thành công',
          count: phongResponse.data.length || 0
        }
      }))
    } catch (error) {
      setApiStatus(prev => ({
        ...prev,
        phong: { 
          status: 'error', 
          message: error.message || 'Không thể kết nối đến API Phòng'
        }
      }))
    }
    
    // Test phiDichVu endpoint
    try {
      const phiDichVuResponse = await phiDichVuService.getAll()
      setApiStatus(prev => ({
        ...prev,
        phiDichVu: { 
          status: 'success', 
          message: 'Kết nối thành công',
          count: phiDichVuResponse.data.length || 0
        }
      }))
    } catch (error) {
      setApiStatus(prev => ({
        ...prev,
        phiDichVu: { 
          status: 'error', 
          message: error.message || 'Không thể kết nối đến API Phí dịch vụ'
        }
      }))
    }
    
    // Test khachHang endpoint
    try {
      const khachHangResponse = await khachHangService.getAll()
      setApiStatus(prev => ({
        ...prev,
        khachHang: { 
          status: 'success', 
          message: 'Kết nối thành công',
          count: khachHangResponse.data.length || 0
        }
      }))
    } catch (error) {
      setApiStatus(prev => ({
        ...prev,
        khachHang: { 
          status: 'error', 
          message: error.message || 'Không thể kết nối đến API Khách hàng'
        }
      }))
    }
    
    // Test hopDong endpoint
    try {
      const hopDongResponse = await hopDongService.getAll()
      setApiStatus(prev => ({
        ...prev,
        hopDong: { 
          status: 'success', 
          message: 'Kết nối thành công',
          count: hopDongResponse.data.length || 0
        }
      }))
    } catch (error) {
      setApiStatus(prev => ({
        ...prev,
        hopDong: { 
          status: 'error', 
          message: error.message || 'Không thể kết nối đến API Hợp đồng'
        }
      }))
    }
    
    setLoading(false)
  }

  useEffect(() => {
    // Kiểm tra kết nối khi component được tải
    testAllConnections()
  }, [])

  const getStatusColor = (status) => {
    switch (status) {
      case 'success':
        return 'success.main'
      case 'error':
        return 'error.main'
      case 'loading':
        return 'info.main'
      default:
        return 'text.secondary'
    }
  }

  const getStatusIcon = (status) => {
    switch (status) {
      case 'success':
        return '✅'
      case 'error':
        return '❌'
      case 'loading':
        return '🔄'
      default:
        return '⏳'
    }
  }

  return (
    <Box sx={{ m: 2 }}>
      <Typography variant="h5" gutterBottom>
        Kiểm tra kết nối API tổng thể
      </Typography>
      
      <Paper elevation={3} sx={{ p: 3, mt: 2 }}>
        <Box sx={{ mb: 3, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
          <Typography variant="h6">
            Trạng thái kết nối các API
          </Typography>
          
          <Button 
            variant="contained" 
            onClick={testAllConnections}
            disabled={loading}
          >
            {loading ? 'Đang kiểm tra...' : 'Kiểm tra lại tất cả'}
          </Button>
        </Box>
        
        {loading && (
          <Box sx={{ display: 'flex', justifyContent: 'center', my: 3 }}>
            <CircularProgress />
          </Box>
        )}
        
        <Grid container spacing={2}>
          {/* Ping API */}
          <Grid item xs={12} md={6}>
            <Card>
              <CardContent>
                <Typography variant="h6" gutterBottom>
                  {getStatusIcon(apiStatus.ping.status)} API Ping
                </Typography>
                <Divider sx={{ mb: 2 }} />
                
                <Typography color={getStatusColor(apiStatus.ping.status)}>
                  <strong>Trạng thái:</strong> {apiStatus.ping.status === 'success' ? 'Thành công' : 
                    apiStatus.ping.status === 'error' ? 'Lỗi' : 'Đang kiểm tra...'}
                </Typography>
                
                {apiStatus.ping.message && (
                  <Typography>
                    <strong>Thông báo:</strong> {apiStatus.ping.message}
                  </Typography>
                )}
                
                {apiStatus.ping.timestamp && (
                  <Typography>
                    <strong>Thời gian:</strong> {apiStatus.ping.timestamp}
                  </Typography>
                )}
              </CardContent>
            </Card>
          </Grid>
          
          {/* ToaNha API */}
          <Grid item xs={12} md={6}>
            <Card>
              <CardContent>
                <Typography variant="h6" gutterBottom>
                  {getStatusIcon(apiStatus.toaNha.status)} API Tòa nhà
                </Typography>
                <Divider sx={{ mb: 2 }} />
                
                <Typography color={getStatusColor(apiStatus.toaNha.status)}>
                  <strong>Trạng thái:</strong> {apiStatus.toaNha.status === 'success' ? 'Thành công' : 
                    apiStatus.toaNha.status === 'error' ? 'Lỗi' : 'Đang kiểm tra...'}
                </Typography>
                
                {apiStatus.toaNha.message && (
                  <Typography>
                    <strong>Thông báo:</strong> {apiStatus.toaNha.message}
                  </Typography>
                )}
                
                {apiStatus.toaNha.status === 'success' && (
                  <Typography>
                    <strong>Số lượng tòa nhà:</strong> {apiStatus.toaNha.count}
                  </Typography>
                )}
              </CardContent>
            </Card>
          </Grid>
          
          {/* Phong API */}
          <Grid item xs={12} md={6}>
            <Card>
              <CardContent>
                <Typography variant="h6" gutterBottom>
                  {getStatusIcon(apiStatus.phong.status)} API Phòng
                </Typography>
                <Divider sx={{ mb: 2 }} />
                
                <Typography color={getStatusColor(apiStatus.phong.status)}>
                  <strong>Trạng thái:</strong> {apiStatus.phong.status === 'success' ? 'Thành công' : 
                    apiStatus.phong.status === 'error' ? 'Lỗi' : 'Đang kiểm tra...'}
                </Typography>
                
                {apiStatus.phong.message && (
                  <Typography>
                    <strong>Thông báo:</strong> {apiStatus.phong.message}
                  </Typography>
                )}
                
                {apiStatus.phong.status === 'success' && (
                  <Typography>
                    <strong>Số lượng phòng:</strong> {apiStatus.phong.count}
                  </Typography>
                )}
              </CardContent>
            </Card>
          </Grid>
          
          {/* PhiDichVu API */}
          <Grid item xs={12} md={6}>
            <Card>
              <CardContent>
                <Typography variant="h6" gutterBottom>
                  {getStatusIcon(apiStatus.phiDichVu.status)} API Phí dịch vụ
                </Typography>
                <Divider sx={{ mb: 2 }} />
                
                <Typography color={getStatusColor(apiStatus.phiDichVu.status)}>
                  <strong>Trạng thái:</strong> {apiStatus.phiDichVu.status === 'success' ? 'Thành công' : 
                    apiStatus.phiDichVu.status === 'error' ? 'Lỗi' : 'Đang kiểm tra...'}
                </Typography>
                
                {apiStatus.phiDichVu.message && (
                  <Typography>
                    <strong>Thông báo:</strong> {apiStatus.phiDichVu.message}
                  </Typography>
                )}
                
                {apiStatus.phiDichVu.status === 'success' && (
                  <Typography>
                    <strong>Số lượng phí dịch vụ:</strong> {apiStatus.phiDichVu.count}
                  </Typography>
                )}
              </CardContent>
            </Card>
          </Grid>
          
          {/* KhachHang API */}
          <Grid item xs={12} md={6}>
            <Card>
              <CardContent>
                <Typography variant="h6" gutterBottom>
                  {getStatusIcon(apiStatus.khachHang.status)} API Khách hàng
                </Typography>
                <Divider sx={{ mb: 2 }} />
                
                <Typography color={getStatusColor(apiStatus.khachHang.status)}>
                  <strong>Trạng thái:</strong> {apiStatus.khachHang.status === 'success' ? 'Thành công' : 
                    apiStatus.khachHang.status === 'error' ? 'Lỗi' : 'Đang kiểm tra...'}
                </Typography>
                
                {apiStatus.khachHang.message && (
                  <Typography>
                    <strong>Thông báo:</strong> {apiStatus.khachHang.message}
                  </Typography>
                )}
                
                {apiStatus.khachHang.status === 'success' && (
                  <Typography>
                    <strong>Số lượng khách hàng:</strong> {apiStatus.khachHang.count}
                  </Typography>
                )}
              </CardContent>
            </Card>
          </Grid>
          
          {/* HopDong API */}
          <Grid item xs={12} md={6}>
            <Card>
              <CardContent>
                <Typography variant="h6" gutterBottom>
                  {getStatusIcon(apiStatus.hopDong.status)} API Hợp đồng
                </Typography>
                <Divider sx={{ mb: 2 }} />
                
                <Typography color={getStatusColor(apiStatus.hopDong.status)}>
                  <strong>Trạng thái:</strong> {apiStatus.hopDong.status === 'success' ? 'Thành công' : 
                    apiStatus.hopDong.status === 'error' ? 'Lỗi' : 'Đang kiểm tra...'}
                </Typography>
                
                {apiStatus.hopDong.message && (
                  <Typography>
                    <strong>Thông báo:</strong> {apiStatus.hopDong.message}
                  </Typography>
                )}
                
                {apiStatus.hopDong.status === 'success' && (
                  <Typography>
                    <strong>Số lượng hợp đồng:</strong> {apiStatus.hopDong.count}
                  </Typography>
                )}
              </CardContent>
            </Card>
          </Grid>
        </Grid>
        
        <Box sx={{ mt: 3 }}>
          <Alert severity="info">
            <Typography variant="body1">
              <strong>Lưu ý:</strong> Nếu các API yêu cầu xác thực, hãy đảm bảo bạn đã đăng nhập trước khi kiểm tra.
            </Typography>
          </Alert>
        </Box>
      </Paper>
    </Box>
  )
}

export default ApiConnectionTest
