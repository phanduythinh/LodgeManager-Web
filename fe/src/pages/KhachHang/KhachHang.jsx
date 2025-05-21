import * as React from 'react'
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
import { useConfirm } from 'material-ui-confirm'
import { getProvinces, getDistrictsByProvinceCode, getWardsByDistrictCode } from 'sub-vn'
import { ToaNhaData } from '../../apis/mock-data'

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

const KhachHangs = [
  {
    MaKhachHang: 'KH-001',
    HoTen: 'Nguyễn Văn A',
    SoDienThoai: '0123456789',
    Email: 'nguyenvana@gmail.com',
    CCCD: '0342000012345',
    GioiTinh: 'Nam',
    NgaySinh: '11/11/1111',
    DiaChiNha: 'Thôn Ba',
    XaPhuong: 'Song Lãng',
    QuanHuyen: 'Vũ Thư',
    TinhThanh: 'Thái Bình'
  },
  {
    MaKhachHang: 'KH-002',
    HoTen: 'Lê Thị B',
    SoDienThoai: '0987654321',
    Email: 'lethib@gmail.com',
    CCCD: '0123450342000',
    GioiTinh: 'Nữ',
    NgaySinh: '22/12/2001',
    DiaChiNha: 'Thôn Trung',
    XaPhuong: 'Song Lãng',
    QuanHuyen: 'Vũ Thư',
    TinhThanh: 'Thái Bình'
  }
]

const listGioiTinh = [...new Set(KhachHangs.map(p => p.GioiTinh))].map(gioitinh => ({ title: gioitinh }))

function KhachHang() {
  const [rows, setRows] = React.useState(KhachHangs)
  const [open, setOpen] = React.useState(false)
  const [formData, setFormData] = React.useState({
    MaKhachHang: '', HoTen: '', SoDienThoai: '', Email: '', CCCD: '', GioiTinh: '', NgaySinh: '', DiaChiNha: '', XaPhuong: '', QuanHuyen: '', TinhThanh: ''
  })
  const [errors, setErrors] = React.useState({})
  const [editId, setEditId] = React.useState(null)
  const [filterStatus, setFilterStatus] = React.useState(null)

  // Mode dia chi
  const [provinces, setProvinces] = React.useState([])
  const [districts, setDistricts] = React.useState([])
  const [wards, setWards] = React.useState([])

  const [searchKeyword, setSearchKeyword] = React.useState('')

  React.useEffect(() => {
    setProvinces(getProvinces())
  }, [])

  // Khi mở dialog sửa, set lại districts và wards theo TinhThanh và QuanHuyen hiện tại
  const handleOpenEdit = (row) => {
    setFormData({
      ...row,
      NgaySinh: row.NgaySinh ? dayjs(row.NgaySinh, 'DD/MM/YYYY') : null
    })
    setErrors({})
    setEditId(row.MaKhachHang)
    setOpen(true)

    if (row.TinhThanh) {
      const province = getProvinces().find(p => p.name === row.TinhThanh)
      if (province) {
        const dsDistricts = getDistrictsByProvinceCode(province.code)
        setDistricts(dsDistricts)

        if (row.QuanHuyen) {
          const district = dsDistricts.find(d => d.name === row.QuanHuyen)
          if (district) {
            setWards(getWardsByDistrictCode(district.code))
          } else {
            setWards([])
          }
        } else {
          setWards([])
        }
      } else {
        setDistricts([])
        setWards([])
      }
    } else {
      setDistricts([])
      setWards([])
    }
  }

  const handleOpenAdd = () => {
    setFormData({
      MaKhachHang: '',
      HoTen: '',
      SoDienThoai: '',
      Email: '',
      CCCD: '',
      GioiTinh: '',
      NgaySinh: '',
      DiaChiNha: '',
      XaPhuong: '',
      QuanHuyen: '',
      TinhThanh: ''
    })
    setErrors({})
    setEditId(null)
    setDistricts([])
    setWards([])
    setOpen(true)
  }

  const handleDelete = (maKH) => {
    setRows(rows.filter(row => row.MaKhachHang !== maKH))
  }

  const handleClose = () => setOpen(false)

  const handleChange = (e) => {
    const { name, value } = e.target
    setFormData(prev => ({ ...prev, [name]: value }))
    if (value.trim() !== '') {
      setErrors(prev => ({ ...prev, [name]: '' }))
    }
  }

  const handleAutoChange = (name, value) => {
    setFormData(prev => ({ ...prev, [name]: value }))
    if (value.trim() !== '') {
      setErrors(prev => ({ ...prev, [name]: '' }))
    }
  }

  const validateForm = () => {
    const requiredFields = ['MaKhachHang', 'HoTen', 'SoDienThoai', 'Email', 'CCCD', 'GioiTinh', 'DiaChiNha', 'TinhThanh', 'QuanHuyen', 'XaPhuong']
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
      NgaySinh: formData.NgaySinh && formData.NgaySinh.format
        ? formData.NgaySinh.format('DD/MM/YYYY')
        : formData.NgaySinh,

    };

    if (editId === null) {
      // Kiểm tra trùng mã khách hàng khi thêm mới
      const isDuplicate = rows.some(r => r.MaKhachHang === newData.MaKhachHang);
      if (isDuplicate) {
        setErrors(prev => ({
          ...prev,
          MaKhachHang: 'Mã khách hàng đã tồn tại'
        }));
        return;
      }

      // Thêm mới
      setRows(prev => [...prev, newData]);
    } else {
      // Sửa thông tin khách hàng
      setRows(prev => {
        return prev.map(row =>
          row.MaKhachHang === editId ? newData : row
        );
      });
    }

    setOpen(false);
  };

  const handleEdit = (id) => {
    const customer = rows.find(row => row.MaKhachHang === id);
    if (customer) {
      setFormData({
        ...customer,
        NgaySinh: customer.NgaySinh ? new Date(customer.NgaySinh) : null,
      });
      setEditId(id);
      setOpen(true);
    }
  };

  const confirmDeleteKhachHang = useConfirm()
  const hanhdleDeleteKhachHang = (row) => {
    confirmDeleteKhachHang({
      title: 'Xóa tòa nhà',
      description: 'Hành động này sẽ xóa vĩnh viễn dữ liệu về Tòa nhà của bạn! Bạn chắc chưa?',
      confirmationText: 'Xóa',
      cancellationText: 'Hủy'
    }).then(() => {
      handleDelete(row.MaKhachHang)
    }).catch(() => { })
  }

  const filteredRows = Array.isArray(rows)
    ? rows.filter(row => {
      const matchesStatus = filterStatus ? row.TrangThai === filterStatus : true
      const keyword = searchKeyword.toLowerCase()
      const matchesSearch =
        row.HoTen.toLowerCase().includes(keyword) ||
        row.MaKhachHang.toLowerCase().includes(keyword) ||
        row.SoDienThoai.toLowerCase().includes(keyword) ||
        row.DiaChiNha.toLowerCase().includes(keyword) ||
        row.XaPhuong.toLowerCase().includes(keyword) ||
        row.QuanHuyen.toLowerCase().includes(keyword) ||
        row.TinhThanh.toLowerCase().includes(keyword)
      return matchesStatus && matchesSearch
    })
    : []

  return (
    <Box sx={{ m: 1 }}>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <h1 style={{ margin: 0 }}>Quản lý khách hàng</h1>
        <Box sx={{
          display: 'flex',
          gap: 2,
          '&:hover': {

          }
        }}>
          <Tooltip title="Thêm khách hàng">
            <Button variant="contained" onClick={handleOpenAdd} sx={{ bgcolor: '#248F55' }}><AddIcon /></Button>
          </Tooltip>
          <Tooltip title="Xuất báo cáo">
            <Button variant="contained" sx={{ bgcolor: '#28C76F' }}><SaveAltIcon /></Button>
          </Tooltip>
        </Box>
      </Box>

      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mt: 1 }}>

        {/* <Autocomplete
          options={status}
          getOptionLabel={(option) => option.title}
          onChange={(e, value) => setFilterStatus(value?.title || null)}
          // getOptionDisabled={(option) => option.title === 'Hoạt động'} // chỉ disable 'Hoạt động'
          sx={{
            width: '45%',
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
        /> */}

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
          placeholder="Tìm kiếm theo tên, mã hoặc số điện thoại"
          value={searchKeyword}
          onChange={(e) => setSearchKeyword(e.target.value)}
          InputProps={{
            startAdornment: (
              <SearchIcon fontSize='small' sx={{ mr: 1 }} />
            )
          }}
        />
      </Box>

      {/* Dialog Thêm/Sửa */}
      <Dialog open={open} onClose={handleClose} maxWidth="md" fullWidth>
        <DialogTitle sx={{ bgcolor: '#EEEEEE', py: 1 }}>{editId === null ? 'Thêm khách hàng' : 'Sửa khách hàng'}</DialogTitle>
        <DialogContent sx={{ display: 'flex', flexDirection: 'column', gap: 2, mt: 1 }}>
          <Grid item xs={6}>
            <TextField
              sx={{ width: 'calc(520.67px/2)' }}
              label="Mã khách hàng (*)"
              fullWidth
              value={formData.MaKhachHang || ''}
              name="MaKhachHang"
              onChange={handleChange}
              error={!!errors.MaKhachHang}
              helperText={errors.MaKhachHang}
              disabled={editId !== null} // Không cho sửa khi edit
            />
          </Grid>

          <Grid container spacing={2} mt={1}>
            <Grid item xs={6}>
              <TextField
                sx={{ width: 'calc(520.67px/2)' }}
                label="Họ và tên (*)"
                fullWidth
                value={formData.HoTen || ''}
                name="HoTen"
                onChange={handleChange}
                error={!!errors.HoTen}
                helperText={errors.HoTen}
              />
            </Grid>
            <Grid item xs={6}>
              <TextField
                sx={{ width: 'calc(520.67px/2)' }}
                label="Số điện thoại (*)"
                fullWidth
                value={formData.SoDienThoai || ''}
                name="SoDienThoai"
                onChange={handleChange}
                error={!!errors.SoDienThoai}
                helperText={errors.SoDienThoai}
              />
            </Grid>
          </Grid>

          <Grid container spacing={2}>
            <Grid item xs={6}>
              <TextField
                sx={{ width: 'calc(520.67px/2)' }}
                label="Email (*)"
                fullWidth
                value={formData.Email || ''}
                name="Email"
                onChange={handleChange}
                error={!!errors.Email}
                helperText={errors.Email}
              />
            </Grid>
            <Grid item xs={6}>
              <LocalizationProvider dateAdapter={AdapterDayjs}>
                <DatePicker
                  label="Ngày sinh"
                  value={formData.NgaySinh || null}
                  onChange={(date) => setFormData(prev => ({ ...prev, NgaySinh: date }))}
                  renderInput={(params) => (
                    <TextField
                      {...params}
                      fullWidth
                      error={!!errors.NgaySinh}
                      helperText={errors.NgaySinh}
                    />
                  )}
                />
              </LocalizationProvider>
            </Grid>
          </Grid>

          <Grid container spacing={2}>
            <Grid item xs={4}>
              <Autocomplete
                sx={{ width: 'calc(520.67px/2)' }}
                disablePortal
                options={listGioiTinh}
                getOptionLabel={(option) => option.title}
                value={listGioiTinh.find(t => t.title === formData.GioiTinh) || null}
                onChange={(e, value) => handleAutoChange('GioiTinh', value?.title || '')}
                renderInput={(params) => (
                  <TextField
                    {...params}
                    label="Giới tính (*)"
                    error={!!errors.GioiTinh}
                    helperText={errors.GioiTinh}
                  />
                )}
              />
            </Grid>
            <Grid item xs={6}>
              <TextField
                sx={{ width: 'calc(520.67px/2)' }}
                label="CMND/CCCD (*)"
                fullWidth
                value={formData.CCCD || ''}
                name="CCCD"
                onChange={handleChange}
                error={!!errors.CCCD}
                helperText={errors.CCCD}
              />
            </Grid>
          </Grid>

          <Box>
            <Grid container spacing={2} mt={1}>
              <Box sx={{ display: 'flex', gap: 2, justifyContent: 'space-between  ' }}>
                <Grid
                  item xs={4}
                  sx={{ width: 'calc(520.67px/2)' }}>
                  <Autocomplete
                    options={provinces}
                    getOptionLabel={(option) => option.name}
                    value={provinces.find(p => p.name === formData.TinhThanh) || null}
                    onChange={(e, value) => {
                      handleAutoChange('TinhThanh', value?.name || '')
                      setDistricts(value ? getDistrictsByProvinceCode(value.code) : [])
                      setFormData(prev => ({ ...prev, QuanHuyen: '', XaPhuong: '' }))
                      setWards([])
                    }}
                    renderInput={(params) => (
                      <TextField {...params} label="Tỉnh/Thành phố (*)" error={!!errors.TinhThanh} helperText={errors.TinhThanh} />
                    )}
                  />
                </Grid>
                <Grid item xs={4} sx={{ width: 'calc(520.67px/2)' }}>
                  <Autocomplete
                    options={districts}
                    getOptionLabel={(option) => option.name}
                    value={districts.find(d => d.name === formData.QuanHuyen) || null}
                    onChange={(e, value) => {
                      handleAutoChange('QuanHuyen', value?.name || '')
                      setWards(value ? getWardsByDistrictCode(value.code) : [])
                      setFormData(prev => ({ ...prev, XaPhuong: '' }))
                    }}
                    renderInput={(params) => (
                      <TextField {...params} label="Quận/Huyện (*)" error={!!errors.QuanHuyen} helperText={errors.QuanHuyen} />
                    )}
                  />
                </Grid>
              </Box>
              <Box sx={{ display: 'flex', gap: 2 }}>
                <Grid item xs={4} sx={{ width: 'calc(520.67px/2)' }}>
                  <Autocomplete
                    options={wards}
                    getOptionLabel={(option) => option.name}
                    value={wards.find(w => w.name === formData.XaPhuong) || null}
                    onChange={(e, value) => handleAutoChange('XaPhuong', value?.name || '')}
                    renderInput={(params) => (
                      <TextField {...params} label="Xã/Phường (*)" error={!!errors.XaPhuong} helperText={errors.XaPhuong} />
                    )}
                  />
                </Grid>
                <Grid item xs={12}>
                  <TextField
                    sx={{ width: 'calc(520.67px/2)' }}
                    label="Địa chỉ chi tiết (*)"
                    fullWidth
                    name="DiaChiNha"
                    value={formData.DiaChiNha || ''}
                    onChange={handleChange}
                    placeholder="91 Nguyễn Chí Thanh"
                    error={!!errors.DiaChiNha}
                    helperText={errors.DiaChiNha}
                  />
                </Grid>
              </Box>
              {/* <Grid item xs={12}>
                <FormControlLabel
                  control={<Switch checked={formData.TrangThai === 'Hoạt động'} onChange={(e) => {
                    setFormData(prev => ({
                      ...prev,
                      TrangThai: e.target.checked ? 'Hoạt động' : 'Không hoạt động'
                    }))
                  }} />}
                  label="Hoạt động"
                />
              </Grid> */}
            </Grid>
          </Box>
        </DialogContent >

        <DialogActions>
          <Button onClick={handleClose}>Hủy</Button>
          <Button onClick={handleSubmit} variant="contained">Lưu</Button>
        </DialogActions>
      </Dialog >


      <TableContainer component={Paper} sx={{ marginTop: '16px' }}>
        <Table sx={{ minWidth: 700 }} aria-label="customized table">
          <TableHead>
            <TableRow>
              <StyledTableCell>Mã KH</StyledTableCell>
              <StyledTableCell>Họ tên</StyledTableCell>
              <StyledTableCell>SĐT</StyledTableCell>
              <StyledTableCell>Ngày sinh</StyledTableCell>
              <StyledTableCell>CMND/CCCD</StyledTableCell>
              <StyledTableCell>Nơi thường trú</StyledTableCell>
              <StyledTableCell align='center'>Tháo tác</StyledTableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {(filteredRows || []).map((row) => (
              <StyledTableRow key={row.MaKhachHang}>
                <StyledTableCell sx={{ p: '8px' }}>{row.MaKhachHang}</StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>{row.HoTen}</StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>{row.SoDienThoai}</StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>{row.NgaySinh}</StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>{row.CCCD}</StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>
                  {`${row.DiaChiNha}, ${row.XaPhuong}, ${row.QuanHuyen}, ${row.TinhThanh}`}
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

export default KhachHang
