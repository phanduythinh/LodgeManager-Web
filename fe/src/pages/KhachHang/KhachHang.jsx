import { useState, useEffect } from 'react'
import { StyledTableCell, StyledTableRow } from '~/components/StyledTable'
import {
  Table, TableBody, TableContainer,
  TableHead, TableRow, Paper, Button, Box, TextField, Dialog, DialogActions,
  DialogContent, DialogTitle, Grid, CircularProgress, Alert, Snackbar
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
import { khachHangService } from '~/apis/services'

import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs'
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider'
import { DatePicker } from '@mui/x-date-pickers/DatePicker'
import dayjs from 'dayjs'

function KhachHang() {
  const [rows, setRows] = useState([])
  const [loading, setLoading] = useState(false)
  const [snackbar, setSnackbar] = useState({
    open: false,
    message: '',
    severity: 'success'
  })
  const [open, setOpen] = useState(false)
  const [formData, setFormData] = useState({
    MaKhachHang: '', HoTen: '', SoDienThoai: '', Email: '', CCCD: '', GioiTinh: '', NgaySinh: '', DiaChiNha: '', XaPhuong: '', QuanHuyen: '', TinhThanh: ''
  })
  const [errors, setErrors] = useState({})
  const [editId, setEditId] = useState(null)
  const [filterStatus, setFilterStatus] = useState(null)

  // Mode dia chi
  const [provinces, setProvinces] = useState([])
  const [districts, setDistricts] = useState([])
  const [wards, setWards] = useState([])

  const [searchKeyword, setSearchKeyword] = useState('')

  useEffect(() => {
    setProvinces(getProvinces())
    fetchKhachHang()
  }, [])

  const fetchKhachHang = async () => {
    try {
      setLoading(true)
      const response = await khachHangService.getAll()
      console.log('KhachHang API response:', response) // Log toàn bộ response để debug

      // Xử lý cả hai trường hợp: mảng trực tiếp hoặc object có thuộc tính data
      let data = [];
      if (Array.isArray(response)) {
        data = response;
      } else if (response && Array.isArray(response.data)) {
        data = response.data;
      }

      // Đảm bảo các trường có giá trị mặc định để tránh undefined
      const processedData = data.map(item => {
        // Chuẩn hóa dữ liệu từ backend (snake_case) sang frontend (camelCase)
        const normalizedItem = {
          // Đảm bảo ID luôn có giá trị
          id: item.id,
          // Thông tin cơ bản
          MaKhachHang: item.MaKhachHang || item.ma_khach_hang || '',
          HoTen: item.HoTen || item.ho_ten || '',
          SoDienThoai: item.SoDienThoai || item.so_dien_thoai || '',
          Email: item.Email || item.email || '',
          // CMND/CCCD - kiểm tra từ nhiều nguồn
          CCCD: item.CCCD || item.cccd || item.CMND_CCCD || '',
          // Ngày sinh
          NgaySinh: item.NgaySinh || item.ngay_sinh || null,
          // Giới tính
          GioiTinh: item.GioiTinh || item.gioi_tinh || 'Nam',
          // Thông tin địa chỉ
          DiaChiNha: item.DiaChiNha || item.dia_chi_nha || '',
          XaPhuong: item.XaPhuong || item.xa_phuong || '',
          QuanHuyen: item.QuanHuyen || item.quan_huyen || '',
          TinhThanh: item.TinhThanh || item.tinh_thanh || ''
        };

        return normalizedItem;
      });

      console.log('KhachHang processed data:', processedData) // Log dữ liệu đã xử lý
      setRows(processedData)
    } catch (error) {
      console.error('Lỗi khi tải dữ liệu khách hàng:', error)
      setSnackbar({
        open: true,
        message: 'Không thể tải danh sách khách hàng',
        severity: 'error'
      })
    } finally {
      setLoading(false)
    }
  }

  // Khi mở dialog sửa, set lại districts và wards theo TinhThanh và QuanHuyen hiện tại
  // const handleOpenEdit = (row) => {
  //   setFormData({
  //     ...row,
  //     NgaySinh: row.NgaySinh ? dayjs(row.NgaySinh, 'DD/MM/YYYY') : null
  //   })
  //   setErrors({})
  //   setEditId(row.MaKhachHang)
  //   setOpen(true)

  //   if (row.TinhThanh) {
  //     const province = getProvinces().find(p => p.name === row.TinhThanh)
  //     if (province) {
  //       const dsDistricts = getDistrictsByProvinceCode(province.code)
  //       setDistricts(dsDistricts)

  //       if (row.QuanHuyen) {
  //         const district = dsDistricts.find(d => d.name === row.QuanHuyen)
  //         if (district) {
  //           setWards(getWardsByDistrictCode(district.code))
  //         } else {
  //           setWards([])
  //         }
  //       } else {
  //         setWards([])
  //       }
  //     } else {
  //       setDistricts([])
  //       setWards([])
  //     }
  //   } else {
  //     setDistricts([])
  //     setWards([])
  //   }
  // }

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

  const handleDelete = async (id) => {
    try {
      setLoading(true)
      await khachHangService.delete(id)
      await fetchKhachHang()
      setSnackbar({
        open: true,
        message: 'Xóa khách hàng thành công',
        severity: 'success'
      })
    } catch (error) {
      console.error('Lỗi khi xóa khách hàng:', error)
      setSnackbar({
        open: true,
        message: 'Không thể xóa khách hàng',
        severity: 'error'
      })
    } finally {
      setLoading(false)
    }
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
        newErrors[field] = 'Vui lòng nhập thông tin này'
      }
    })

    if (!formData.NgaySinh) {
      newErrors.NgaySinh = 'Vui lòng chọn ngày sinh'
    }

    setErrors(newErrors)
    return Object.keys(newErrors).length === 0
  }

  const handleSubmit = async () => {
    if (!validateForm()) return;

    try {
      setLoading(true);

      // Tìm khách hàng hiện tại nếu đang cập nhật
      let currentCustomer = null;
      if (editId) {
        currentCustomer = rows.find(row => row.id === editId || row.MaKhachHang === editId);
        console.log('Dữ liệu khách hàng hiện tại:', currentCustomer);
      }

      // Chuẩn bị dữ liệu để gửi đến backend
      // Đảm bảo giữ lại các trường cũ nếu không được nhập lại
      const baseData = {
        ma_khach_hang: formData.MaKhachHang,
        ho_ten: formData.HoTen,
        ngay_sinh: formData.NgaySinh && formData.NgaySinh.format
          ? formData.NgaySinh.format('YYYY-MM-DD')
          : formData.NgaySinh,
        gioi_tinh: formData.GioiTinh?.toLowerCase() || 'nam',
        so_dien_thoai: formData.SoDienThoai || '',
        email: formData.Email || '',
        cccd: formData.CCCD || (currentCustomer?.CCCD || currentCustomer?.CMND_CCCD || ''),
        dia_chi_nha: formData.DiaChiNha || (currentCustomer?.DiaChiNha || currentCustomer?.DiaChi || ''),
        xa_phuong: formData.XaPhuong || (currentCustomer?.XaPhuong || ''),
        quan_huyen: formData.QuanHuyen || (currentCustomer?.QuanHuyen || ''),
        tinh_thanh: formData.TinhThanh || (currentCustomer?.TinhThanh || '')
      };

      // Nếu đang cập nhật, giữ lại ID để backend có thể tìm được bản ghi
      const khachHangData = editId ? {
        ...baseData,
        id: currentCustomer?.id || editId // Đảm bảo có ID để backend tìm được bản ghi
      } : baseData;

      console.log('Dữ liệu khách hàng gửi đến backend:', khachHangData);

      let response;
      if (editId === null) {
        // Kiểm tra trùng mã khách hàng khi thêm mới
        const isDuplicate = rows.some(r => r.MaKhachHang === khachHangData.ma_khach_hang);
        if (isDuplicate) {
          setErrors(prev => ({
            ...prev,
            MaKhachHang: 'Mã khách hàng đã tồn tại'
          }));
          setLoading(false);
          return;
        }

        // Gọi API để thêm mới khách hàng
        response = await khachHangService.create(khachHangData);
        console.log('Kết quả thêm mới khách hàng:', response);

        setSnackbar({
          open: true,
          message: 'Thêm khách hàng thành công',
          severity: 'success'
        });
      } else {
        try {
          // Log ID và dữ liệu trước khi gọi API
          console.log('ID khách hàng cần cập nhật:', editId);
          console.log('Dữ liệu gửi đi:', khachHangData);

          // Gọi API để cập nhật khách hàng
          response = await khachHangService.update(editId, khachHangData);
          console.log('Kết quả cập nhật khách hàng:', response);

          setSnackbar({
            open: true,
            message: 'Cập nhật khách hàng thành công',
            severity: 'success'
          });
        } catch (error) {
          console.error('Lỗi khi cập nhật khách hàng:', error.response?.data);
          setSnackbar({
            open: true,
            message: `Lỗi khi cập nhật khách hàng: ${error.response?.data?.message || error.message}`,
            severity: 'error'
          });
          setLoading(false);
          return;
        }
      }

      // Tải lại danh sách khách hàng sau khi thêm/sửa
      await fetchKhachHang();

      setOpen(false);
    } catch (error) {
      console.error('Lỗi khi lưu khách hàng:', error);
      setSnackbar({
        open: true,
        message: `Lỗi khi ${editId === null ? 'thêm' : 'cập nhật'} khách hàng: ${error.message || 'Không xác định'}`,
        severity: 'error'
      });
    } finally {
      setLoading(false);
    }
  };

  const handleEdit = (id) => {
    const customer = rows.find(row => row.MaKhachHang === id || row.id === id);
    if (customer) {
      console.log('Dữ liệu khách hàng gốc khi chỉnh sửa:', customer);

      // Đảm bảo tất cả các trường có giá trị, giữ lại dữ liệu cũ khi cập nhật
      const customerData = {
        id: customer.id, // Đảm bảo có ID để cập nhật
        MaKhachHang: customer.MaKhachHang || customer.ma_khach_hang || '',
        HoTen: customer.HoTen || customer.ho_ten || '',
        SoDienThoai: customer.SoDienThoai || customer.so_dien_thoai || '',
        // Giữ lại email cũ khi cập nhật
        Email: customer.Email || customer.email || '',
        // Giữ lại CCCD cũ khi cập nhật
        CCCD: customer.CCCD || customer.cccd || customer.CMND_CCCD || '',
        DiaChiNha: customer.DiaChiNha || customer.dia_chi_nha || customer.DiaChi || '',
        XaPhuong: customer.XaPhuong || customer.xa_phuong || '',
        QuanHuyen: customer.QuanHuyen || customer.quan_huyen || '',
        TinhThanh: customer.TinhThanh || customer.tinh_thanh || '',
        GioiTinh: customer.GioiTinh || customer.gioi_tinh || 'Nam',
      };

      // Chuyển đổi định dạng ngày tháng nếu cần
      const ngaySinh = customer.NgaySinh ?
        (typeof customer.NgaySinh === 'string' ?
          dayjs(customer.NgaySinh) :
          dayjs(customer.NgaySinh)) :
        customer.ngay_sinh ? dayjs(customer.ngay_sinh) : null;

      setFormData({
        ...customerData,
        NgaySinh: ngaySinh
      });

      // Lưu cả ID số và mã khách hàng để sử dụng khi cập nhật
      setEditId(customer.id || customer.MaKhachHang);
      setOpen(true);

      // Cập nhật các dropdown địa chỉ
      if (customer.TinhThanh || customer.tinh_thanh) {
        const province = provinces.find(p => p.name === (customer.TinhThanh || customer.tinh_thanh));
        if (province) {
          const dsDistricts = getDistrictsByProvinceCode(province.code);
          setDistricts(dsDistricts);

          if (customer.QuanHuyen || customer.quan_huyen) {
            const district = dsDistricts.find(d => d.name === (customer.QuanHuyen || customer.quan_huyen));
            if (district) {
              setWards(getWardsByDistrictCode(district.code));
            }
          }
        }
      }

      console.log('Dữ liệu khách hàng đã xử lý khi chỉnh sửa:', customerData);
      console.log('ID khách hàng để cập nhật:', customer.id || customer.MaKhachHang);
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

  const listGioiTinh = [
    { title: 'nam' },
    { title: 'nữ' }
  ]

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
                  label="Ngày sinh (*)"
                  value={formData.NgaySinh || null}
                  onChange={(date) => {
                    setFormData(prev => ({ ...prev, NgaySinh: date }));
                    if (date) {
                      setErrors(prev => ({ ...prev, NgaySinh: '' }));
                    }
                  }}
                  format="DD/MM/YYYY"
                  slotProps={{
                    textField: {
                      fullWidth: true,
                      error: !!errors.NgaySinh,
                      helperText: errors.NgaySinh
                    }
                  }}
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
      </Dialog>

      <TableContainer component={Paper} sx={{ marginTop: '16px' }}>
        <Table sx={{ minWidth: 700 }} aria-label="customized table">
          <TableHead>
            <TableRow>
              <StyledTableCell>Mã KH</StyledTableCell>
              <StyledTableCell>Họ tên</StyledTableCell>
              {/* <StyledTableCell>Giới tính</StyledTableCell> */}
              <StyledTableCell>SĐT</StyledTableCell>
              <StyledTableCell>Ngày sinh</StyledTableCell>
              <StyledTableCell>CMND/CCCD</StyledTableCell>
              <StyledTableCell>Nơi thường trú</StyledTableCell>
              <StyledTableCell align='center'>Thao tác</StyledTableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {filteredRows.map((row) => (
              <StyledTableRow key={row.MaKhachHang || row.id}>
                <StyledTableCell sx={{ p: '8px' }}>
                  {row.MaKhachHang}
                  <Box sx={{ color: '#B9B9C3' }}>ID: {row.id}</Box>
                </StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>{row.HoTen}</StyledTableCell>
                {/* <StyledTableCell sx={{ p: '8px' }}>{row.GioiTinh || row.gioi_tinh}</StyledTableCell> */}
                <StyledTableCell sx={{ p: '8px' }}>{row.SoDienThoai}</StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>{row.NgaySinh ? new Date(row.NgaySinh).toLocaleDateString('vi-VN') : ''}</StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>{row.CCCD || row.cccd || row.CMND_CCCD || ''}</StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>
                  {[row.DiaChiNha || row.dia_chi_nha, row.XaPhuong || row.xa_phuong, row.QuanHuyen || row.quan_huyen, row.TinhThanh || row.tinh_thanh]
                    .filter(Boolean).join(', ')}
                </StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>
                  <Box sx={{ display: 'flex', gap: 1, justifyContent: 'center' }}>
                    <Tooltip title="Sửa">
                      <Button
                        variant="contained"
                        sx={{ bgcolor: '#828688' }}
                        onClick={() => handleEdit(row.id || row.MaKhachHang)}
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
      {loading && (
        <Box sx={{ display: 'flex', justifyContent: 'center', mt: 3 }}>
          <CircularProgress />
        </Box>
      )}
      <Snackbar
        open={snackbar.open}
        autoHideDuration={6000}
        onClose={() => setSnackbar(prev => ({ ...prev, open: false }))}
        anchorOrigin={{ vertical: 'top', horizontal: 'right' }}
      >
        <Alert
          onClose={() => setSnackbar(prev => ({ ...prev, open: false }))}
          severity={snackbar.severity}
          sx={{ width: '100%' }}
        >
          {snackbar.message}
        </Alert>
      </Snackbar>
    </Box>
  )
}

export default KhachHang
