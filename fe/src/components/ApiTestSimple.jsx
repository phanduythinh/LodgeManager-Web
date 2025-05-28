import { useState, useEffect } from 'react'
import { Box, Typography, Button, CircularProgress, Paper } from '@mui/material'
import { phongService, phiDichVuService, khachHangService, hopDongService, toaNhaService } from '~/apis/services'

function ApiTestSimple() {
  const [results, setResults] = useState({})
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    testAllApis()
  }, [])

  const testAllApis = async () => {
    setLoading(true)
    const testResults = {}

    try {
      // Test ToaNha API
      const toaNhaResponse = await toaNhaService.getAll()
      testResults.toaNha = {
        success: true,
        data: Array.isArray(toaNhaResponse) ? toaNhaResponse : 
              (toaNhaResponse && Array.isArray(toaNhaResponse.data)) ? toaNhaResponse.data : [],
        count: Array.isArray(toaNhaResponse) ? toaNhaResponse.length : 
              (toaNhaResponse && Array.isArray(toaNhaResponse.data)) ? toaNhaResponse.data.length : 0
      }
    } catch (error) {
      testResults.toaNha = { success: false, error: error.message }
    }

    try {
      // Test Phong API
      const phongResponse = await phongService.getAll()
      testResults.phong = {
        success: true,
        data: Array.isArray(phongResponse) ? phongResponse : 
              (phongResponse && Array.isArray(phongResponse.data)) ? phongResponse.data : [],
        count: Array.isArray(phongResponse) ? phongResponse.length : 
              (phongResponse && Array.isArray(phongResponse.data)) ? phongResponse.data.length : 0
      }
    } catch (error) {
      testResults.phong = { success: false, error: error.message }
    }

    try {
      // Test PhiDichVu API
      const phiDichVuResponse = await phiDichVuService.getAll()
      testResults.phiDichVu = {
        success: true,
        data: Array.isArray(phiDichVuResponse) ? phiDichVuResponse : 
              (phiDichVuResponse && Array.isArray(phiDichVuResponse.data)) ? phiDichVuResponse.data : [],
        count: Array.isArray(phiDichVuResponse) ? phiDichVuResponse.length : 
              (phiDichVuResponse && Array.isArray(phiDichVuResponse.data)) ? phiDichVuResponse.data.length : 0
      }
    } catch (error) {
      testResults.phiDichVu = { success: false, error: error.message }
    }

    try {
      // Test KhachHang API
      const khachHangResponse = await khachHangService.getAll()
      testResults.khachHang = {
        success: true,
        data: Array.isArray(khachHangResponse) ? khachHangResponse : 
              (khachHangResponse && Array.isArray(khachHangResponse.data)) ? khachHangResponse.data : [],
        count: Array.isArray(khachHangResponse) ? khachHangResponse.length : 
              (khachHangResponse && Array.isArray(khachHangResponse.data)) ? khachHangResponse.data.length : 0
      }
    } catch (error) {
      testResults.khachHang = { success: false, error: error.message }
    }

    try {
      // Test HopDong API
      const hopDongResponse = await hopDongService.getAll()
      testResults.hopDong = {
        success: true,
        data: Array.isArray(hopDongResponse) ? hopDongResponse : 
              (hopDongResponse && Array.isArray(hopDongResponse.data)) ? hopDongResponse.data : [],
        count: Array.isArray(hopDongResponse) ? hopDongResponse.length : 
              (hopDongResponse && Array.isArray(hopDongResponse.data)) ? hopDongResponse.data.length : 0
      }
    } catch (error) {
      testResults.hopDong = { success: false, error: error.message }
    }

    setResults(testResults)
    setLoading(false)
  }

  const renderTestResult = (name, result) => {
    if (!result) return null

    return (
      <Paper elevation={3} sx={{ p: 2, mb: 2 }}>
        <Typography variant="h6" gutterBottom>
          {name}: {result.success ? 
            <span style={{ color: 'green' }}>Thành công</span> : 
            <span style={{ color: 'red' }}>Thất bại</span>}
        </Typography>
        
        {result.success ? (
          <>
            <Typography variant="body1">Số lượng bản ghi: {result.count}</Typography>
            <Typography variant="body2" sx={{ mt: 1 }}>
              Dữ liệu mẫu: {result.data && result.data.length > 0 ? 
                JSON.stringify(result.data[0], null, 2).substring(0, 200) + '...' : 
                'Không có dữ liệu'}
            </Typography>
          </>
        ) : (
          <Typography variant="body1" color="error">
            Lỗi: {result.error}
          </Typography>
        )}
      </Paper>
    )
  }

  return (
    <Box sx={{ p: 3 }}>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 3 }}>
        <Typography variant="h4" gutterBottom>
          Kiểm tra API đơn giản
        </Typography>
        <Button 
          variant="contained" 
          onClick={testAllApis} 
          disabled={loading}
        >
          {loading ? <CircularProgress size={24} /> : 'Kiểm tra lại'}
        </Button>
      </Box>

      {loading ? (
        <Box sx={{ display: 'flex', justifyContent: 'center', my: 5 }}>
          <CircularProgress />
        </Box>
      ) : (
        <Box>
          {renderTestResult('Tòa Nhà', results.toaNha)}
          {renderTestResult('Phòng', results.phong)}
          {renderTestResult('Phí Dịch Vụ', results.phiDichVu)}
          {renderTestResult('Khách Hàng', results.khachHang)}
          {renderTestResult('Hợp Đồng', results.hopDong)}
        </Box>
      )}
    </Box>
  )
}

export default ApiTestSimple
