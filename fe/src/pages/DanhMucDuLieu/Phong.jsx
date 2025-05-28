import React, { useState, useEffect } from 'react'
import { phongService } from '~/apis/services'
import {
  Table, TableBody, TableContainer, TableHead, TableRow, Paper, 
  Box, CircularProgress, Typography
} from '@mui/material'
import { StyledTableCell, StyledTableRow } from '~/components/StyledTable'
import { formatCurrency } from '~/components/formatCurrency'

function Phong() {
  const [rooms, setRooms] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const fetchPhong = async () => {
      try {
        setLoading(true)
        const response = await phongService.getAll()
        console.log('API response:', response)
        
        // Xử lý dữ liệu
        let data = []
        if (Array.isArray(response)) {
          data = response
        } else if (response && Array.isArray(response.data)) {
          data = response.data
        }
        
        setRooms(data)
      } catch (error) {
        console.error('Lỗi khi lấy danh sách phòng:', error)
      } finally {
        setLoading(false)
      }
    }
    
    fetchPhong()
  }, [])
  
  if (loading) {
    return (
      <Box sx={{ display: 'flex', justifyContent: 'center', mt: 3 }}>
        <CircularProgress />
      </Box>
    )
  }
  
  return (
    <Box sx={{ p: 2 }}>
      <Typography variant="h6" sx={{ mb: 2 }}>Danh sách phòng</Typography>
      
      <TableContainer component={Paper}>
        <Table>
          <TableHead>
            <TableRow>
              <StyledTableCell>Mã phòng</StyledTableCell>
              <StyledTableCell>Thông tin phòng</StyledTableCell>
              <StyledTableCell align="right">Giá thuê</StyledTableCell>
              <StyledTableCell align="right">Đặt cọc</StyledTableCell>
              <StyledTableCell align="right">Diện tích</StyledTableCell>
              <StyledTableCell align="right">Số khách</StyledTableCell>
              <StyledTableCell align="center">Trạng thái</StyledTableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {rooms.length === 0 ? (
              <TableRow>
                <StyledTableCell colSpan={7} align="center">Không có dữ liệu</StyledTableCell>
              </TableRow>
            ) : (
              rooms.map((room) => (
                <StyledTableRow key={room.MaPhong || room.id}>
                  <StyledTableCell>{room.MaPhong}</StyledTableCell>
                  <StyledTableCell>
                    <Box>{room.TenPhong}</Box>
                    <Box sx={{ color: '#B9B9C3' }}>Tòa nhà: {room.TenNha}</Box>
                    <Box sx={{ color: '#B9B9C3' }}>{room.Tang}</Box>
                  </StyledTableCell>
                  <StyledTableCell align="right">{formatCurrency(room.GiaThue)}</StyledTableCell>
                  <StyledTableCell align="right">{formatCurrency(room.DatCoc)}</StyledTableCell>
                  <StyledTableCell align="right">{room.DienTich} m²</StyledTableCell>
                  <StyledTableCell align="right">{room.SoKhachToiDa}</StyledTableCell>
                  <StyledTableCell align="center">
                    <span
                      style={{
                        padding: '4px 8px',
                        borderRadius: '12px',
                        color: room.TrangThai === 'Đang ở' ? '#388e3c' : '#EA5455',
                        backgroundColor: room.TrangThai === 'Đang ở' ? '#c8e6c9' : '#EA54551F',
                        fontWeight: 600,
                        fontSize: '13px',
                        display: 'inline-block',
                        textAlign: 'center',
                        minWidth: '80px'
                      }}
                    >
                      {room.TrangThai}
                    </span>
                  </StyledTableCell>
                </StyledTableRow>
              ))
            )}
          </TableBody>
        </Table>
      </TableContainer>
    </Box>
  )
}

export default Phong
