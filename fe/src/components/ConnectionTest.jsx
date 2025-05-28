import { useState, useEffect } from 'react'
import {
  Box,
  Typography,
  Paper,
  Alert,
  CircularProgress,
  Button
} from '@mui/material'
import axios from 'axios'

function ConnectionTest() {
  const [status, setStatus] = useState('idle') // 'idle', 'loading', 'success', 'error'
  const [message, setMessage] = useState('')
  const [timestamp, setTimestamp] = useState('')

  const testConnection = async () => {
    setStatus('loading')
    try {
      const response = await axios.get('http://127.0.0.1:8000/api/ping', {
        withCredentials: true
      })
      
      setStatus('success')
      setMessage(response.data.message)
      setTimestamp(response.data.timestamp)
    } catch (error) {
      console.error('Lỗi kết nối:', error)
      setStatus('error')
      setMessage(error.message || 'Không thể kết nối đến máy chủ')
    }
  }

  useEffect(() => {
    // Kiểm tra kết nối khi component được tải
    testConnection()
  }, [])

  return (
    <Box sx={{ m: 2 }}>
      <Typography variant="h5" gutterBottom>
        Kiểm tra kết nối API
      </Typography>
      
      <Paper elevation={3} sx={{ p: 3, mt: 2, maxWidth: 600 }}>
        <Typography variant="h6" gutterBottom>
          Trạng thái kết nối
        </Typography>
        
        {status === 'loading' && (
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
            <CircularProgress size={24} />
            <Typography>Đang kiểm tra kết nối...</Typography>
          </Box>
        )}
        
        {status === 'success' && (
          <Alert severity="success" sx={{ mb: 2 }}>
            <Typography><strong>Trạng thái:</strong> Kết nối thành công</Typography>
            <Typography><strong>Thông báo:</strong> {message}</Typography>
            <Typography><strong>Thời gian:</strong> {timestamp}</Typography>
          </Alert>
        )}
        
        {status === 'error' && (
          <Alert severity="error" sx={{ mb: 2 }}>
            <Typography><strong>Trạng thái:</strong> Kết nối thất bại</Typography>
            <Typography><strong>Lỗi:</strong> {message}</Typography>
          </Alert>
        )}
        
        <Button 
          variant="contained" 
          onClick={testConnection}
          disabled={status === 'loading'}
          sx={{ mt: 2 }}
        >
          Kiểm tra lại kết nối
        </Button>
      </Paper>
    </Box>
  )
}

export default ConnectionTest
