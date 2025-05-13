import * as React from 'react'
import { styled } from '@mui/material/styles'
import Table from '@mui/material/Table'
import TableBody from '@mui/material/TableBody'
import TableCell, { tableCellClasses } from '@mui/material/TableCell'
import TableContainer from '@mui/material/TableContainer'
import TableHead from '@mui/material/TableHead'
import TableRow from '@mui/material/TableRow'
import Paper from '@mui/material/Paper'
import Button from '@mui/material/Button'
import Box from '@mui/material/Box'

const StyledTableCell = styled(TableCell)(({ theme }) => ({
  [`&.${tableCellClasses.head}`]: {
    backgroundColor: '#a8d8fb',
    borderRight: '1px solid #e0e0e0'
  },
  [`&.${tableCellClasses.body}`]: {
    fontSize: 14,
    borderRight: '1px solid #e0e0e0'
  },
}));

const StyledTableRow = styled(TableRow)(({ theme }) => ({
  '&:nth-of-type(odd)': {
    backgroundColor: theme.palette.action.hover,
  }
}));

function createData(name, calories, fat, carbs, protein, a, b, c, d, e) {
  return { name, calories, fat, carbs, protein, a, b, c, d, e };
}

const rows = [
  createData('KH001', 'Nguyễn Văn A', 'Nam', '14/01/2003','0123345534', 'pdt1401@gmail.com', '0121232', 'co nhue 2, bac tu liem, ha noi', 'Nha A', '01'),
  createData('KH002', 'Nguyễn Văn B', 'Nu', '14/01/2003','0123345534', 'pdt1401@gmail.com', '0121232', 'co nhue 2, bac tu liem, ha noi', 'Nha A', '01')
];

function ManageCustomers() {
  return (
    <Box>
      <h1>Quản lý khách hàng</h1>
      <Box sx={{ display: 'flex', gap: 2 }}>
        <Button variant="contained" fontSize='small'>Thêm khách hàng mới</Button>
        <Button variant="contained" fontSize='small'>Xuất báo cáo</Button>
      </Box>

      <TableContainer component={Paper} sx={{ marginTop: '16px' }}>
        <Table sx={{ minWidth: 700 }} aria-label="customized table">
          <TableHead>
            <TableRow>
              <StyledTableCell>ID khách hàng</StyledTableCell>
              <StyledTableCell>Họ và tên</StyledTableCell>
              <StyledTableCell>Giới tính</StyledTableCell>
              <StyledTableCell>Ngày sinh</StyledTableCell>
              <StyledTableCell>Số điện thoại</StyledTableCell>
              <StyledTableCell>Email</StyledTableCell>
              <StyledTableCell>CCCD</StyledTableCell>
              <StyledTableCell>Địa chỉ thường trú</StyledTableCell>
              <StyledTableCell>Nhà thuê</StyledTableCell>
              <StyledTableCell>Phòng thuê</StyledTableCell>
              <StyledTableCell>Chức năng</StyledTableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {rows.map((row) => (
              <StyledTableRow key={row.name}>
                <StyledTableCell component="th" scope="row"> {row.name}</StyledTableCell>
                <StyledTableCell>{row.calories}</StyledTableCell>
                <StyledTableCell>{row.fat}</StyledTableCell>
                <StyledTableCell>{row.carbs}</StyledTableCell>
                <StyledTableCell>{row.protein}</StyledTableCell>
                <StyledTableCell>{row.a}</StyledTableCell>
                <StyledTableCell>{row.b}</StyledTableCell>
                <StyledTableCell>{row.c}</StyledTableCell>
                <StyledTableCell>{row.d}</StyledTableCell>
                <StyledTableCell>{row.e}</StyledTableCell>
                <StyledTableCell>
                  <Box sx={{ display: 'flex', gap: 1 }}>
                  <Button variant="contained">Sửa</Button>
                  <Button variant="contained">Xóa</Button>
                  </Box>
                </StyledTableCell>
              </StyledTableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </Box>
  )
}

export default ManageCustomers
