import { useState, useEffect } from 'react'
import { StyledTableCell, StyledTableRow } from '~/components/StyledTable'
import {
  Table, TableBody, TableContainer,
  TableHead, TableRow, Paper, Button, Box, TextField, Dialog, DialogActions,
  DialogContent, DialogTitle, Grid, Switch, FormControlLabel, CircularProgress, Alert, Snackbar
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
import { toaNhaService } from '~/apis/services'

function ToaNha() {
  const [rows, setRows] = useState([])
  const [open, setOpen] = useState(false)
  const [loading, setLoading] = useState(false)
  const [formData, setFormData] = useState({
    MaNha: '', TenNha: '', SoPhong: '', DiaChiNha: '', TrangThai: 'Hoạt động',
    TinhThanh: '', QuanHuyen: '', XaPhuong: '', Phongs: [], PhiDicuVus: []
  })
  const [errors, setErrors] = useState({})
  const [editId, setEditId] = useState(null)
  const [filterStatus, setFilterStatus] = useState(null)
  const [snackbar, setSnackbar] = useState({
    open: false,
    message: '',
    severity: 'success'
  })

  // Mode dia chi
  const [provinces, setProvinces] = useState([])
  const [districts, setDistricts] = useState([])
  const [wards, setWards] = useState([])

  const [searchKeyword, setSearchKeyword] = useState('')

  useEffect(() => {
    setProvinces(getProvinces())
    fetchToaNha()
  }, [])

  const fetchToaNha = async () => {
    try {
      setLoading(true)
      const response = await toaNhaService.getAll()
      // Xử lý cả hai trường hợp: mảng trực tiếp hoặc object có thuộc tính data
      const data = Array.isArray(response) ? response : 
                 (response && response.data) ? response.data : []
      console.log('ToaNha data:', data) // Log để debug
      setRows(data)
    } catch (error) {
      console.error('Lỗi khi lấy danh sách tòa nhà:', error)
      setSnackbar({
        open: true,
        message: 'Không thể tải danh sách tòa nhà',
        severity: 'error'
      })
    } finally {
      setLoading(false)
    }
  }

  // Khi mở dialog sửa, set lại districts và wards theo TinhThanh và QuanHuyen hiện tại
  const handleOpenEdit = (row) => {
    setFormData(row)
    setErrors({})
    setEditId(row.MaNha)
    setOpen(true)

    // Cập nhật districts theo TinhThanh hiện tại
    if (row.TinhThanh) {
      const province = getProvinces().find(p => p.name === row.TinhThanh)
      if (province) {
        const dsDistricts = getDistrictsByProvinceCode(province.code)
        setDistricts(dsDistricts)

        // Cập nhật wards theo QuanHuyen hiện tại
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
      MaNha: '',
      TenNha: '',
      SoPhong: '',
      DiaChiNha: '',
      TinhThanh: '',
      QuanHuyen: '',
      XaPhuong: '',
      TrangThai: 'Không hoạt động' // Mặc định là không hoạt động
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
      await toaNhaService.delete(id)
      await fetchToaNha()
      setSnackbar({
        open: true,
        message: 'Xóa tòa nhà thành công',
        severity: 'success'
      })
    } catch (error) {
      console.error('Lỗi khi xóa tòa nhà:', error)
      let errorMessage = 'Có lỗi xảy ra khi xóa tòa nhà'
      if (error.response && error.response.data && error.response.data.message) {
        errorMessage = error.response.data.message
      }
      setSnackbar({
        open: true,
        message: errorMessage,
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

  const validateForm = (isEdit = false) => {
    const requiredFields = ['MaNha', 'TenNha', 'DiaChiNha', 'TinhThanh', 'QuanHuyen', 'XaPhuong']
    const newErrors = {}

    requiredFields.forEach(field => {
      if (!formData[field] || formData[field].trim() === '') {
        newErrors[field] = 'Thông tin bắt buộc'
      }
    })

    // Kiểm tra trùng mã nhà khi thêm mới
    if (formData.MaNha && !isEdit) {
      const duplicate = rows.find(row => row.MaNha === formData.MaNha)
      if (duplicate) {
        newErrors.MaNha = 'Mã nhà đã tồn tại'
      }
    }

    setErrors(newErrors)
    return Object.keys(newErrors).length === 0
  }

  const handleSubmit = async () => {
    if (!validateForm(editId !== null)) return

    try {
      setLoading(true)
      const newData = { ...formData }

      if (editId === null) {
        // Thêm mới
        await toaNhaService.create(newData)
        setSnackbar({
          open: true,
          message: 'Thêm tòa nhà thành công',
          severity: 'success'
        })
      } else {
        // Sửa: tìm theo id
        const toaNha = rows.find(row => row.MaNha === editId)
        if (toaNha) {
          await toaNhaService.update(toaNha.id, newData)
          setSnackbar({
            open: true,
            message: 'Cập nhật tòa nhà thành công',
            severity: 'success'
          })
        }
      }

      // Tải lại danh sách tòa nhà
      await fetchToaNha()
      setOpen(false)
    } catch (error) {
      console.error('Lỗi khi lưu tòa nhà:', error)
      let errorMessage = 'Có lỗi xảy ra khi lưu tòa nhà'
      if (error.response && error.response.data && error.response.data.message) {
        errorMessage = error.response.data.message
      }
      setSnackbar({
        open: true,
        message: errorMessage,
        severity: 'error'
      })
    } finally {
      setLoading(false)
    }
  }

  const status = [
    { title: 'Hoạt động' },
    { title: 'Không hoạt động' }
  ]

  const confirmDeleteToaNha = useConfirm()
  const hanhdleDeleteToaNha = (row) => {
    confirmDeleteToaNha({
      title: 'Xóa tòa nhà',
      description: 'Hành động này sẽ xóa vĩnh viễn dữ liệu về Tòa nhà của bạn! Bạn chắc chưa?',
      confirmationText: 'Xóa',
      cancellationText: 'Hủy'
    }).then(() => {
      handleDelete(row.id)
    }).catch(() => { })
  }

  const filteredRows = Array.isArray(rows)
    ? rows.filter(row => {
      const matchesStatus = filterStatus ? row.TrangThai === filterStatus : true
      const keyword = searchKeyword.toLowerCase()
      const matchesSearch =
        row.TenNha.toLowerCase().includes(keyword) ||
        row.MaNha.toLowerCase().includes(keyword) ||
        row.DiaChiNha.toLowerCase().includes(keyword)
      return matchesStatus && matchesSearch
    })
    : []

  const handleCloseSnackbar = () => {
    setSnackbar(prev => ({ ...prev, open: false }))
  }

  return (
    <Box sx={{ m: 1 }}>
      <Snackbar 
        open={snackbar.open} 
        autoHideDuration={6000} 
        onClose={handleCloseSnackbar}
        anchorOrigin={{ vertical: 'top', horizontal: 'right' }}
      >
        <Alert onClose={handleCloseSnackbar} severity={snackbar.severity} sx={{ width: '100%' }}>
          {snackbar.message}
        </Alert>
      </Snackbar>

      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <h1 style={{ margin: 0 }}>Quản lý tòa nhà</h1>
        <Box sx={{
          display: 'flex',
          gap: 2
        }}>
          <Tooltip title="Thêm tòa nhà">
            <Button 
              variant="contained" 
              onClick={handleOpenAdd} 
              sx={{ bgcolor: '#248F55' }}
              disabled={loading}
            >
              {loading ? <CircularProgress size={24} /> : <AddIcon />}
            </Button>
          </Tooltip>
          <Tooltip title="Xuất báo cáo">
            <Button variant="contained" sx={{ bgcolor: '#28C76F' }}><SaveAltIcon /></Button>
          </Tooltip>
        </Box>
      </Box>

      <Box sx={{ display: 'flex', gap: 2, justifyContent: 'space-between', alignItems: 'center', mt: 1 }}>
        <Autocomplete
          options={status}
          getOptionLabel={(option) => option.title}
          onChange={(e, value) => setFilterStatus(value?.title || null)}
          sx={{
            width: '100%',
            '& .MuiInputBase-root': {
              paddingTop: 0,
              paddingBottom: 0
            },
            '& .MuiFormLabel-root': {
              top: -7.5
            }
          }}
          renderInput={(params) => (
            <TextField {...params} label="Trạng thái" />
          )}
        />

        <TextField
          sx={{
            width: '100%',
            '& .MuiFormLabel-root': {
              top: -5
            }
          }}
          placeholder="Tìm kiếm theo tên, mã hoặc địa chỉ"
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
        <DialogTitle sx={{ bgcolor: '#EEEEEE', py: 1 }}>{editId === null ? 'Thêm tòa nhà' : 'Sửa tòa nhà'}</DialogTitle>
        <DialogContent sx={{ display: 'flex', flexDirection: 'column', gap: 2, mt: 1 }}>

          <Box>
            <strong>Dự án/Tòa nhà</strong>
            <Grid container spacing={2} mt={1}>
              <Grid item xs={6}>
                <TextField
                  label="Tên tòa nhà (*)"
                  fullWidth
                  value={formData.TenNha || ''}
                  name="TenNha"
                  onChange={handleChange}
                  error={!!errors.TenNha}
                  helperText={errors.TenNha}
                />
              </Grid>
              <Grid item xs={6}>
                <TextField
                  label="Tên viết tắt/Mã tòa (*)"
                  fullWidth
                  value={formData.MaNha || ''}
                  name="MaNha"
                  onChange={handleChange}
                  disabled={editId !== null} // Không cho sửa khi đang ở chế độ sửa
                  error={!!errors.MaNha}
                  helperText={errors.MaNha}
                />
              </Grid>
            </Grid>
          </Box>

          <Box>
            <strong>Thông tin địa chỉ</strong>
            <Grid container spacing={2} mt={1} sx={{ display: 'flex', flexDirection: 'column' }}>
              <Box sx={{ display: 'flex', gap: 1, justifyContent: 'space-between' }}>
                <Grid
                  item xs={4}
                  sx={{ width: 'calc(538px / 3)' }}>
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
                <Grid item xs={4} sx={{ width: 'calc(538px / 3)' }}>
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
                <Grid item xs={4} sx={{ width: 'calc(538px / 3)' }}>
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
              </Box>

              <Grid item xs={12}>
                <TextField
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
              <Grid item xs={12}>
                <FormControlLabel
                  control={<Switch checked={formData.TrangThai === 'Hoạt động'} onChange={(e) => {
                    setFormData(prev => ({
                      ...prev,
                      TrangThai: e.target.checked ? 'Hoạt động' : 'Không hoạt động'
                    }))
                  }} />}
                  label="Hoạt động"
                />
              </Grid>
            </Grid>
          </Box>
        </DialogContent>

        <DialogActions>
          <Button onClick={handleClose}>Hủy</Button>
          <Button 
            onClick={handleSubmit} 
            variant="contained"
            disabled={loading}
          >
            {loading ? <CircularProgress size={24} /> : 'Lưu'}
          </Button>
        </DialogActions>
      </Dialog>

      {loading && (
        <Box sx={{ display: 'flex', justifyContent: 'center', mt: 3 }}>
          <CircularProgress />
        </Box>
      )}

      {!loading && (
        <TableContainer component={Paper} sx={{ marginTop: '16px' }}>
          <Table sx={{ minWidth: 700 }} aria-label="customized table">
            <TableHead>
              <TableRow>
                <StyledTableCell>Mã tòa nhà</StyledTableCell>
                <StyledTableCell>Tên tòa nhà</StyledTableCell>
                <StyledTableCell>Số phòng</StyledTableCell>
                <StyledTableCell>Địa chỉ</StyledTableCell>
                <StyledTableCell align='center'>Trạng thái</StyledTableCell>
                <StyledTableCell align='center'>Thao tác</StyledTableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {filteredRows.length === 0 ? (
                <TableRow>
                  <StyledTableCell colSpan={6} align="center">Không có dữ liệu</StyledTableCell>
                </TableRow>
              ) : (
                filteredRows.map((row) => (
                  <StyledTableRow key={row.MaNha}>
                    <StyledTableCell sx={{ p: '8px' }}>{row.MaNha}</StyledTableCell>
                    <StyledTableCell sx={{ p: '8px' }}>{row.TenNha}</StyledTableCell>
                    <StyledTableCell sx={{ p: '8px' }}>{Array.isArray(row.Phongs) ? row.Phongs.length : 0}</StyledTableCell>
                    <StyledTableCell sx={{ p: '8px' }}>
                      {`${row.DiaChiNha}, ${row.XaPhuong}, ${row.QuanHuyen}, ${row.TinhThanh}`}
                    </StyledTableCell>
                    <StyledTableCell align='center' sx={{ p: '8px' }}>
                      <span
                        style={{
                          padding: '4px 8px',
                          borderRadius: '12px',
                          color: row.TrangThai === 'Hoạt động' ? '#388e3c' : '#EA5455',
                          backgroundColor: row.TrangThai === 'Hoạt động' ? '#c8e6c9' : '#EA54551F',
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
                            onClick={() => hanhdleDeleteToaNha(row)}
                          >
                            <DeleteIcon fontSize='small' />
                          </Button>
                        </Tooltip>
                      </Box>
                    </StyledTableCell>
                  </StyledTableRow>
                ))
              )}
            </TableBody>
          </Table>
        </TableContainer>
      )}
    </Box>
  )
}

export default ToaNha
