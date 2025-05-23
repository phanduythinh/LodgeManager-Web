import { useState, useEffect } from 'react'
import { styled } from '@mui/material/styles'
import {
  Table, TableBody, TableCell, tableCellClasses, TableContainer,
  TableHead, TableRow, Paper, Button, Box, TextField, Dialog, DialogActions,
  DialogContent, DialogTitle, Grid, Switch, FormControlLabel
} from '@mui/material'
import Autocomplete from '@mui/material/Autocomplete'
import AddIcon from '@mui/icons-material/Add'
import SaveAltIcon from '@mui/icons-material/SaveAlt'
import Tooltip from '@mui/material/Tooltip'
import DeleteIcon from '@mui/icons-material/Delete'
import BorderColorIcon from '@mui/icons-material/BorderColor'
import SearchIcon from '@mui/icons-material/Search'
import PersonAddAltIcon from '@mui/icons-material/PersonAddAlt'
import AddCircleOutlineIcon from '@mui/icons-material/AddCircleOutline'

import { useConfirm } from 'material-ui-confirm'
import { getProvinces, getDistrictsByProvinceCode, getWardsByDistrictCode } from 'sub-vn'
import { ToaNhaData, HopDongs } from '../../apis/mock-data'
import { DemoContainer } from '@mui/x-date-pickers/internals/demo'
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs'
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider'
import { DatePicker } from '@mui/x-date-pickers/DatePicker'
import dayjs from 'dayjs'

const StyledTableCell = styled(TableCell)(() => ({
  [`&.${tableCellClasses.head}`]: {
    backgroundColor: '#a8d8fb',
    borderRight: '1px solid #e0e0e0'
  },
  [`&.${tableCellClasses.body}`]: {
    fontSize: 14,
    borderRight: '1px solid #e0e0e0'
  }
}))

const StyledTableRow = styled(TableRow)(({ theme }) => ({
  '&:nth-of-type(odd)': {
    backgroundColor: theme.palette.action.hover
  }
}))

function HopDong() {
  const [rows, setRows] = useState(HopDongs)
  const [listToaNha, setListToaNha] = useState(ToaNhaData);
  const [listPhong, setListPhong] = useState([]);

  const [open, setOpen] = useState(false)
  const [openClient, setOpenClient] = useState(false)
  const [formData, setFormData] = useState({
    MaHopDong: '', MaNhaId: '', MaPhongId: '', NgayBatDau: '', NgayKetThuc: '', TienThue: '', TienCoc: '', ChuKyThanhToan: '', NgayTinhTien: '', KhachHangs: '', MaDichVuIds: '', TrangThai: ''
  })
  const [errors, setErrors] = useState({})
  const [editId, setEditId] = useState(null)
  const [filterStatus, setFilterStatus] = useState(null)
  const [searchKeyword, setSearchKeyword] = useState('')

  const handleOpenEdit = (row) => {
    setFormData({
      MaHopDong: row.MaHopDong || '',
      MaNhaId: row.MaNha || '',       // cập nhật đúng mã tòa nhà
      MaPhongId: row.MaPhong || '',   // cập nhật đúng mã phòng
      NgayBatDau: row.NgayBatDau ? dayjs(row.NgayBatDau, 'DD/MM/YYYY') : null,
      NgayKetThuc: row.NgayKetThuc ? dayjs(row.NgayKetThuc, 'DD/MM/YYYY') : null,
      NgayTinhTien: row.NgayTinhTien ? dayjs(row.NgayTinhTien, 'DD/MM/YYYY') : null,
      TienThue: row.TienThue || '',
      TienCoc: row.TienCoc || '',
      ChuKyThanhToan: row.ChuKyThanhToan || '',
      KhachHangs: row.KhachHangs || '',
      MaDichVuIds: row.MaDichVuIds || '',
      TrangThai: row.TrangThai || ''
    });

    setErrors({});
    setEditId(row.MaHopDong);  // nên là MaHopDong chứ không phải MaKhachHang
    setOpen(true);
  };


  const handleOpenAdd = () => {
    setFormData({
      MaHopDong: '', MaNhaId: '', MaPhongId: '', NgayBatDau: '', NgayKetThuc: '', TienThue: '', TienCoc: '', ChuKyThanhToan: '', NgayTinhTien: '', KhachHangs: '', MaDichVuIds: '', TrangThai: ''
    })
    setErrors({})
    setEditId(null)
    setOpen(true)
  }

  const handleDelete = (maKH) => {
    setRows(rows.filter(row => row.MaHopDong !== maKH))
  }

  const handleClose = () => setOpen(false)

  const handleChange = (e) => {
    const { name, value } = e.target
    setFormData(prev => ({ ...prev, [name]: value }))
    if (value.trim() !== '') {
      setErrors(prev => ({ ...prev, [name]: '' }))
    }
  }

  const handleAutoChange = (field, value) => {
    setFormData((prev) => ({
      ...prev,
      [field]: value,
      ...(field === 'MaNhaId' && { MaPhongId: '' }) // reset phòng khi đổi tòa nhà
    }));

    if (field === 'MaNhaId') {
      const selectedToaNha = listToaNha.find(n => n.MaNha === value);
      if (selectedToaNha) {
        setListPhong(selectedToaNha.Phongs || []);
      } else {
        setListPhong([]);
      }
    }
  };

  const validateForm = () => {
    const requiredFields = ['MaHopDong', 'NgayBatDau', 'NgayKetThuc', 'TienThue', 'NgayTinhTien', 'MaKhachHangs']
    const newErrors = {}

    requiredFields.forEach(field => {
      if (!formData[field] || formData[field].trim() === '') {
        newErrors[field] = 'Thông tin bắt buộc'
      }
    })

    setErrors(newErrors)
    return Object.keys(newErrors).length === 0
  }

  const handleSubmit = () => {
    if (!validateForm()) return;

    const newData = {
      ...formData,
      NgayBatDau: formData.NgayBatDau && formData.NgayBatDau.format
        ? formData.NgayBatDau.format('DD/MM/YYYY')
        : formData.NgayBatDau,
      NgayKetThuc: formData.NgayKetThuc && formData.NgayKetThuc.format
        ? formData.NgayKetThuc.format('DD/MM/YYYY')
        : formData.NgayKetThuc,
      NgayTinhTien: formData.NgayTinhTien && formData.NgayTinhTien.format
        ? formData.NgayTinhTien.format('DD/MM/YYYY')
        : formData.NgayTinhTien
    };

    if (editId === null) {
      // Kiểm tra trùng mã hợp đồng khi thêm mới
      const isDuplicate = rows.some(r => r.MaHopDong === newData.MaHopDong);
      if (isDuplicate) {
        setErrors(prev => ({
          ...prev,
          MaHopDong: 'Mã khách hàng đã tồn tại'
        }));
        return;
      }

      // Thêm mới
      setRows(prev => [...prev, newData]);
    } else {
      // Sửa thông tin khách hàng
      setRows(prev => {
        return prev.map(row =>
          row.MaHopDong === editId ? newData : row
        );
      });
    }

    setOpen(false);
  };

  const confirmDeleteKhachHang = useConfirm()
  const hanhdleDeleteKhachHang = (row) => {
    confirmDeleteKhachHang({
      title: 'Xóa hợp đồng',
      description: 'Hành động này sẽ xóa vĩnh viễn hợp đồng này. Bạn chắc chắn muốn xóa?',
      confirmationText: 'Xóa',
      cancellationText: 'Hủy'
    }).then(() => {
      handleDelete(row.MaHopDong)
    }).catch(() => { })
  }

  // Tìm kiếm
  const filteredRows = Array.isArray(rows)
    ? rows.filter(row => {
      const matchesStatus = filterStatus ? row.TrangThai === filterStatus : true
      const keyword = searchKeyword.toLowerCase()
      const matchesSearch =
        row.MaHopDong.toLowerCase().includes(keyword) ||
        row.TrangThai.toLowerCase().includes(keyword)
      return matchesStatus && matchesSearch
    })
    : []

  const listChuKyThanhToan = [
    { title: '1 tháng' },
    { title: '2 tháng' },
    { title: '3 tháng' },
    { title: '4 tháng' },
    { title: '5 tháng' },
    { title: '6 tháng' },
    { title: '7 tháng' },
    { title: '8 tháng' },
    { title: '9 tháng' },
    { title: '10 tháng' },
    { title: '11 tháng' },
    { title: '1 năm' },
    { title: '2 năm' },
    { title: '3 năm' }
  ]

  const listTrangThai = [
    { title: 'Còn hạn' },
    { title: 'Hết hạn' }
  ]

  return (
    <Box sx={{ m: 1 }}>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <h1 style={{ margin: 0 }}>Quản lý hợp đồng</h1>
        <Box sx={{
          display: 'flex',
          gap: 2,
          '&:hover': {

          }
        }}>
          <Tooltip title="Thêm hợp đồng">
            <Button variant="contained" onClick={handleOpenAdd} sx={{ bgcolor: '#248F55' }}><AddIcon /></Button>
          </Tooltip>
          <Tooltip title="Xuất báo cáo">
            <Button variant="contained" sx={{ bgcolor: '#28C76F' }}><SaveAltIcon /></Button>
          </Tooltip>
        </Box>
      </Box>

      <Box sx={{ display: 'flex', gap: 2, justifyContent: 'space-between', alignItems: 'center', mt: 1 }}>

        <Autocomplete
          options={listTrangThai}
          getOptionLabel={(option) => option?.title || ''}
          onChange={(e, value) => setFilterStatus(value?.title || null)}
          sx={{
            width: '100%',
            '& .MuiInputBase-root': {
              paddingTop: 0,
              paddingBottom: 0
            },
            '& .MuiFormLabel-root': {
              top: -7.5 // tuỳ chỉnh nếu label bị lệch
            }
          }}
          renderInput={(params) => (
            <TextField {...params} label="Trạng thái" />
          )}
        />

        <TextField
          sx={{
            width: '100%',
            '& .toolpad-demo-app-x0zmg8-MuiInputBase-input-MuiOutlinedInput-input': {
              py: '7.5px'
            },
            '& .MuiFormLabel-root': {
              top: -5
            }
          }}
          placeholder="Tìm kiếm theo mã hợp đồng"
          value={searchKeyword}
          onChange={(e) => setSearchKeyword(e.target.value)}
          InputProps={{
            startAdornment: (
              <SearchIcon fontSize='small' sx={{ mr: 1 }} />
            )
          }}
        />
      </Box>

      {/* Dialog Thêm/Sửa hợp đồng */}
      <Dialog open={open} onClose={handleClose} maxWidth="md" fullWidth>
        <DialogTitle sx={{ bgcolor: '#EEEEEE', py: 1 }}>{editId === null ? 'Thêm hợp đồng' : 'Sửa hợp đồng'}</DialogTitle>
        <DialogContent sx={{ display: 'flex', flexDirection: 'column', gap: 2, mt: 1 }}>
          <Grid container spacing={2} sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
            <strong>1. Thông tin chung</strong>
            <Grid item xs={6}>
              <TextField
                sx={{ width: 'calc(520.67px/2)' }}
                label="Mã hợp đồng (*)"
                fullWidth
                value={formData.MaHopDong || ''}
                name="MaHopDong"
                onChange={handleChange}
                error={!!errors.MaHopDong}
                helperText={errors.MaHopDong}
                disabled={editId !== null} // Không cho sửa khi edit
              />
            </Grid>

            <Grid container spacing={2}>
              <Grid item xs={4}>
                <Autocomplete
                  sx={{ width: 'calc(520.67px/2)' }}
                  disablePortal
                  options={listToaNha}
                  getOptionLabel={(option) => option.TenNha || ''}
                  value={listToaNha.find(t => t.MaNha === formData.MaNhaId) || null}
                  onChange={(e, value) => handleAutoChange('MaNhaId', value?.MaNha || '')}
                  renderInput={(params) => (
                    <TextField
                      {...params}
                      label="Tòa nhà (*)"
                      error={!!errors.MaNhaId}
                      helperText={errors.MaNhaId}
                    />
                  )}
                />

              </Grid>
              <Grid item xs={4}>
                <Autocomplete
                  sx={{ width: 'calc(520.67px/2)' }}
                  disablePortal
                  options={listPhong}
                  getOptionLabel={(option) => option.TenPhong || ''}
                  value={listPhong.find((option) => option.MaPhong === formData.MaPhongId) || null}
                  onChange={(e, value) => handleAutoChange('MaPhongId', value?.MaPhong || '')}
                  renderInput={(params) => (
                    <TextField
                      {...params}
                      label="Phòng (*)"
                      error={!!errors.MaPhongId}
                      helperText={errors.MaPhongId}
                    />
                  )}
                />

              </Grid>
            </Grid>

            <Grid container spacing={2}>
              <Grid item xs={6}>
                <LocalizationProvider dateAdapter={AdapterDayjs}>
                  <DatePicker
                    sx={{ width: 'calc(520.67px/2)' }}
                    label="Ngày bắt đầu"
                    value={formData.NgayBatDau || null}
                    onChange={(date) => setFormData(prev => ({ ...prev, NgayBatDau: date }))}
                    format="DD/MM/YYYY"
                    renderInput={(params) => (
                      <TextField
                        {...params}
                        fullWidth
                        error={!!errors.NgayBatDau}
                        helperText={errors.NgayBatDau}
                      />
                    )}
                  />
                </LocalizationProvider>
              </Grid>
              <Grid item xs={6}>
                <LocalizationProvider dateAdapter={AdapterDayjs}>
                  <DatePicker
                    sx={{ width: 'calc(520.67px/2)' }}
                    label="Ngày kết thúc"
                    value={formData.NgayKetThuc || null}
                    onChange={(date) => setFormData(prev => ({ ...prev, NgayKetThuc: date }))}
                    format="DD/MM/YYYY"
                    renderInput={(params) => (
                      <TextField
                        {...params}
                        fullWidth
                        error={!!errors.NgayKetThuc}
                        helperText={errors.NgayKetThuc}
                      />
                    )}
                  />
                </LocalizationProvider>
              </Grid>
            </Grid>
          </Grid>

          <strong>2. Khách hàng</strong>

          <Grid container spacing={2} sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
            <strong>3. Tiền thuê & Tiền cọc</strong>
            <Grid container spacing={2}>
              <Grid container spacing={2}>
                <Grid item xs={6}>
                  <TextField
                    sx={{ width: 'calc(520.67px/2)' }}
                    label="Tiền thuê (*)"
                    fullWidth
                    value={formData.TienThue || ''}
                    name="TienThue"
                    onChange={handleChange}
                    error={!!errors.TienThue}
                    helperText={errors.TienThue}
                  />
                </Grid>
                <Grid item xs={4}>
                  <Autocomplete
                    sx={{ width: 'calc(520.67px/2)' }}
                    disablePortal
                    options={listChuKyThanhToan}
                    getOptionLabel={(option) => option.title || ''}
                    value={listChuKyThanhToan.find(t => t.title === formData.ChuKyThanhToan) || null}
                    onChange={(e, value) => handleAutoChange('ChuKyThanhToan', value?.title || '')}
                    renderInput={(params) => (
                      <TextField
                        {...params}
                        label="Chu kỳ thanh toán (*)"
                        error={!!errors.ChuKyThanhToan}
                        helperText={errors.ChuKyThanhToan}
                      />
                    )}
                  />
                </Grid>
              </Grid>
              <Grid container spacing={2}>
                <Grid item xs={6}>
                  <TextField
                    sx={{ width: 'calc(520.67px/2)' }}
                    label="Tiền cọc (*)"
                    fullWidth
                    value={formData.TienCoc || ''}
                    name="TienCoc"
                    onChange={handleChange}
                    error={!!errors.TienCoc}
                    helperText={errors.TienCoc}
                  />
                </Grid>
                <Grid item xs={6}>
                  <LocalizationProvider dateAdapter={AdapterDayjs}>
                    <DatePicker
                      sx={{ width: 'calc(520.67px/2)' }}
                      label="Ngày bắt đầu tính tiền"
                      value={formData.NgayTinhTien || null}
                      onChange={(date) => setFormData(prev => ({ ...prev, NgayTinhTien: date }))}
                      format="DD/MM/YYYY"
                      renderInput={(params) => (
                        <TextField
                          {...params}
                          fullWidth
                          error={!!errors.NgayTinhTien}
                          helperText={errors.NgayTinhTien}
                        />
                      )}
                    />
                  </LocalizationProvider>
                </Grid>
              </Grid>
            </Grid>
          </Grid>

          <strong>4. Tiền phí dịch vụ</strong>

        </DialogContent >

        <DialogActions>
          <Button onClick={handleClose}>Hủy</Button>
          <Button onClick={handleSubmit} variant="contained">Lưu</Button>
        </DialogActions>
      </Dialog >



      {/* Bảng */}
      <TableContainer component={Paper} sx={{ marginTop: '16px' }}>
        <Table sx={{ minWidth: 700 }} aria-label="customized table">
          <TableHead>
            <TableRow>
              <StyledTableCell>Mã HĐ</StyledTableCell>
              <StyledTableCell>Đại diện</StyledTableCell>
              <StyledTableCell>Giá thuê</StyledTableCell>
              <StyledTableCell>Ngày bắt đầu</StyledTableCell>
              <StyledTableCell>Ngày kết thúc</StyledTableCell>
              <StyledTableCell>Trạng thái</StyledTableCell>
              <StyledTableCell align='center'>Tháo tác</StyledTableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {(filteredRows || []).map((row) => (
              <StyledTableRow key={row.MaHopDong}>
                <StyledTableCell sx={{ p: '8px' }}>{row.MaHopDong}</StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>
                  <Box>{row.KhachHangs[0].HoTen}</Box>
                  <Box sx={{ color: '#B9B9C3' }}>Tòa nhà: {row.MaNhaId}</Box>
                  <Box sx={{ color: '#B9B9C3' }}>Tầng: {row.MaPhongId}</Box>
                </StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>{row.TienThue}</StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>{row.NgayBatDau}</StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>{row.NgayKetThuc}</StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>
                  <span
                    style={{
                      padding: '4px 8px',
                      borderRadius: '12px',
                      color: row.TrangThai === 'Còn hạn' ? '#388e3c' : '#EA5455',
                      backgroundColor: row.TrangThai === 'Còn hạn' ? '#c8e6c9' : '#EA54551F',
                      fontWeight: 600,
                      fontSize: '13px',
                      display: 'inline-block',
                      textAlign: 'center',
                      minWidth: '80px'
                    }}
                  >
                    {row.TrangThai}
                  </span>
                </StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>
                  <Box sx={{ display: 'flex', gap: 1, justifyContent: 'center' }}>
                    <Tooltip title="Sửa">
                      <Button
                        variant="contained"
                        sx={{ bgcolor: '#828688' }}
                        onClick={() => handleOpenEdit(row)}
                      >
                        <BorderColorIcon fontSize='small' />
                      </Button>
                    </Tooltip>
                    <Tooltip title="Xóa">
                      <Button
                        variant="contained"
                        sx={{ bgcolor: '#EA5455' }}
                        onClick={() => hanhdleDeleteKhachHang(row)}
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
    </Box >
  )
}

export default HopDong
