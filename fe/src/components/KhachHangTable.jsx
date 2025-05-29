import React from 'react';
import { 
  Table, TableBody, TableContainer, TableHead, TableRow, 
  Paper, Button, Box, Tooltip 
} from '@mui/material';
import BorderColorIcon from '@mui/icons-material/BorderColor';
import DeleteIcon from '@mui/icons-material/Delete';
import { StyledTableCell, StyledTableRow } from './StyledTable';
import { formatCCCD, formatAddress } from '../utils/formatUtils';

/**
 * Component hiển thị bảng khách hàng
 */
const KhachHangTable = ({ data, onEdit, onDelete }) => {
  // Hàm xử lý hiển thị ngày tháng
  const formatDate = (dateString) => {
    if (!dateString) return '';
    try {
      return new Date(dateString).toLocaleDateString('vi-VN');
    } catch (error) {
      console.error('Lỗi khi định dạng ngày:', error);
      return dateString;
    }
  };

  return (
    <TableContainer component={Paper} sx={{ marginTop: '16px' }}>
      <Table sx={{ minWidth: 700 }} aria-label="bảng khách hàng">
        <TableHead>
          <TableRow>
            <StyledTableCell>Mã KH</StyledTableCell>
            <StyledTableCell>Họ tên</StyledTableCell>
            <StyledTableCell>SĐT</StyledTableCell>
            <StyledTableCell>Ngày sinh</StyledTableCell>
            <StyledTableCell>CMND/CCCD</StyledTableCell>
            <StyledTableCell>Nơi thường trú</StyledTableCell>
            <StyledTableCell align='center'>Thao tác</StyledTableCell>
          </TableRow>
        </TableHead>
        <TableBody>
          {data.map((row) => (
            <StyledTableRow key={row.MaKhachHang || row.id}>
              <StyledTableCell sx={{ p: '8px' }}>{row.MaKhachHang}</StyledTableCell>
              <StyledTableCell sx={{ p: '8px' }}>{row.HoTen}</StyledTableCell>
              <StyledTableCell sx={{ p: '8px' }}>{row.SoDienThoai}</StyledTableCell>
              <StyledTableCell sx={{ p: '8px' }}>{formatDate(row.NgaySinh)}</StyledTableCell>
              <StyledTableCell sx={{ p: '8px' }}>{formatCCCD(row)}</StyledTableCell>
              <StyledTableCell sx={{ p: '8px' }}>{formatAddress(row)}</StyledTableCell>
              <StyledTableCell sx={{ p: '8px' }}>
                <Box sx={{ display: 'flex', gap: 1, justifyContent: 'center' }}>
                  <Tooltip title="Sửa">
                    <Button
                      variant="contained"
                      sx={{ bgcolor: '#828688' }}
                      onClick={() => onEdit(row.MaKhachHang)}
                    >
                      <BorderColorIcon fontSize='small' />
                    </Button>
                  </Tooltip>
                  <Tooltip title="Xóa">
                    <Button
                      variant="contained"
                      sx={{ bgcolor: '#EA5455' }}
                      onClick={() => onDelete(row)}
                    >
                      <DeleteIcon fontSize='small' />
                    </Button>
                  </Tooltip>
                </Box>
              </StyledTableCell>
            </StyledTableRow>
          ))}
        </TableBody>
      </Table>
    </TableContainer>
  );
};

export default KhachHangTable;
