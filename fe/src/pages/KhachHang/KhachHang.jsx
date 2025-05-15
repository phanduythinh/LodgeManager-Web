import * as React from 'react'
import { styled } from '@mui/material/styles'
import {
  Table, TableBody, TableCell, tableCellClasses, TableContainer,
  TableHead, TableRow, Paper, Button, Box, TextField, Dialog, DialogActions,
  DialogContent, DialogTitle
} from '@mui/material'

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

// Fake data
function createData(id, name, gender, dob, phone, email, cccd, address, house, room) {
  return { id, name, gender, dob, phone, email, cccd, address, house, room };
}

const initialRows = [
  createData('KH001', 'Nguyễn Văn A', 'Nam', '14/01/2003', '0123345534', 'pdt1401@gmail.com', '0121232', 'Cổ nhuế 2', 'Nhà A', '01'),
  createData('KH002', 'Nguyễn Văn B', 'Nữ', '14/01/2003', '0123345534', 'pdt1401@gmail.com', '0121232', 'Cổ nhuế 2', 'Nhà A', '02')
];

function KhachHang() {
  const [rows, setRows] = React.useState(initialRows);
  const [open, setOpen] = React.useState(false);
  const [formData, setFormData] = React.useState({
    id: '', name: '', gender: '', dob: '', phone: '',
    email: '', cccd: '', address: '', house: '', room: ''
  });
  const [errors, setErrors] = React.useState({});
  const [editIndex, setEditIndex] = React.useState(null);

  const handleDelete = (id) => {
    setRows(rows.filter(row => row.id !== id));
  };

  const handleOpenAdd = () => {
    setFormData({
      id: '', name: '', gender: '', dob: '', phone: '',
      email: '', cccd: '', address: '', house: '', room: ''
    });
    setErrors({});
    setEditIndex(null);
    setOpen(true);
  };

  const handleOpenEdit = (row, index) => {
    setFormData(row);
    setErrors({});
    setEditIndex(index);
    setOpen(true);
  };

  const handleClose = () => setOpen(false);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
    if (value.trim() !== '') {
      setErrors(prev => ({ ...prev, [name]: '' }));
    }
  };

  const validateForm = () => {
    const newErrors = {};

    Object.entries(formData).forEach(([key, value]) => {
      const val = value.trim();

      // Kiểm tra rỗng
      if (val === '') {
        newErrors[key] = 'Không được bỏ trống trường này';
      }

      // Kiểm tra định dạng email
      if (key === 'email' && val !== '') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(val)) {
          newErrors[key] = 'Email không hợp lệ';
        }
      }

      // Kiểm tra số điện thoại: 10 chữ số
      if (key === 'phone' && val !== '') {
        const phoneRegex = /^\d{10}$/;
        if (!phoneRegex.test(val)) {
          newErrors[key] = 'Số điện thoại phải có 10 chữ số';
        }
      }

      // Kiểm tra CCCD: 12 chữ số
      if (key === 'cccd' && val !== '') {
        const cccdRegex = /^\d{12}$/;
        if (!cccdRegex.test(val)) {
          newErrors[key] = 'CCCD phải có 12 chữ số';
        }
      }
    });
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = () => {
    if (!validateForm()) return;

    if (editIndex === null) {
      setRows([...rows, formData]);
    } else {
      const updated = [...rows];
      updated[editIndex] = formData;
      setRows(updated);
    }
    setOpen(false);
  };

  return (
    <Box>
      <h1>Quản lý khách hàng</h1>
      <Box sx={{ display: 'flex', gap: 2 }}>
        <Button variant="contained" onClick={handleOpenAdd}>Thêm khách hàng mới</Button>
        <Button variant="contained">Xuất báo cáo</Button>
      </Box>

      {/* Dialog Thêm/Sửa */}
      <Dialog open={open} onClose={handleClose}>
        <DialogTitle>{editIndex === null ? 'Thêm khách hàng' : 'Sửa khách hàng'}</DialogTitle>
        <DialogContent sx={{ display: 'flex', flexDirection: 'column', gap: 2, mt: 1 }}>
          {Object.keys(formData).map(key => (
            <TextField
              key={key}
              name={key}
              label={key.toUpperCase()}
              value={formData[key]}
              onChange={handleChange}
              error={Boolean(errors[key])}
              helperText={errors[key]}
              fullWidth
              size="small"
            />
          ))}
        </DialogContent>

        <DialogActions>
          <Button onClick={handleClose}>Hủy</Button>
          <Button onClick={handleSubmit} variant="contained">
            {editIndex === null ? 'Thêm' : 'Lưu'}
          </Button>
        </DialogActions>
      </Dialog>

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
            {rows.map((row, index) => (
              <StyledTableRow key={row.id}>
                <StyledTableCell>{row.id}</StyledTableCell>
                <StyledTableCell>{row.name}</StyledTableCell>
                <StyledTableCell>{row.gender}</StyledTableCell>
                <StyledTableCell>{row.dob}</StyledTableCell>
                <StyledTableCell>{row.phone}</StyledTableCell>
                <StyledTableCell>{row.email}</StyledTableCell>
                <StyledTableCell>{row.cccd}</StyledTableCell>
                <StyledTableCell>{row.address}</StyledTableCell>
                <StyledTableCell>{row.house}</StyledTableCell>
                <StyledTableCell>{row.room}</StyledTableCell>
                <StyledTableCell>
                  <Box sx={{ display: 'flex', gap: 1 }}>
                    <Button variant="contained" onClick={() => handleOpenEdit(row, index)}>Sửa</Button>
                    <Button variant="contained" color="error" onClick={() => handleDelete(row.id)}>Xóa</Button>
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

export default KhachHang
