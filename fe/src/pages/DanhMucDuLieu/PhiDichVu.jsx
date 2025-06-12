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
import { formatCurrency } from '~/components/formatCurrency'
import { phiDichVuService, toaNhaService } from '~/apis/services'

function PhiDichVu() {
  const [rows, setRows] = useState([])
  const [toaNhas, setToaNhas] = useState([])
  const [loading, setLoading] = useState(false)
  const [snackbar, setSnackbar] = useState({
    open: false,
    message: '',
    severity: 'success'
  })
  const [open, setOpen] = useState(false)
  const [formData, setFormData] = useState({
    MaDichVu: '',
    MaNhaId: '',
    TenDichVu: '',
    LoaiDichVu: '',
    DonGia: '',
    DonViTinh: '',
    TenNha: ''
  })
  const [errors, setErrors] = useState({})
  const [editIndex, setEditIndex] = useState(null)
  const [filterToaNha, setFilterToaNha] = useState(null)
  const [filterLoai, setFilterLoai] = useState(null)
  const [searchText, setSearchText] = useState('')
  const [selectedTenNha, setSelectedTenNha] = useState(null)
  const confirm = useConfirm()

  useEffect(() => {
    fetchPhiDichVu()
    fetchToaNha()
  }, [])

  const fetchPhiDichVu = async () => {
    try {
      setLoading(true)
      const response = await phiDichVuService.getAll()
      console.log('PhiDichVu API response:', response) // Log toàn bộ response để debug
      
      // Xử lý cả hai trường hợp: mảng trực tiếp hoặc object có thuộc tính data
      let data = [];
      if (Array.isArray(response)) {
        data = response;
      } else if (response && Array.isArray(response.data)) {
        data = response.data;
      }
      
      console.log('PhiDichVu processed data:', data) // Log dữ liệu đã xử lý
      setRows(data)
    } catch (error) {
      console.error('Lỗi khi lấy danh sách phí dịch vụ:', error)
      setSnackbar({
        open: true,
        message: 'Không thể tải danh sách phí dịch vụ',
        severity: 'error'
      })
    } finally {
      setLoading(false)
    }
  }

  const fetchToaNha = async () => {
    try {
      const response = await toaNhaService.getAll()
      setToaNhas(response.data)
    } catch (error) {
      console.error('Lỗi khi lấy danh sách tòa nhà:', error)
    }
  }

  const handleDelete = async (id) => {
    try {
      setLoading(true)
      await phiDichVuService.delete(id)
      await fetchPhiDichVu()
      setSnackbar({
        open: true,
        message: 'Xóa phí dịch vụ thành công',
        severity: 'success'
      })
    } catch (error) {
      console.error('Lỗi khi xóa phí dịch vụ:', error)
      setSnackbar({
        open: true,
        message: 'Không thể xóa phí dịch vụ',
        severity: 'error'
      })
    } finally {
      setLoading(false)
    }
  }

  const handleOpenAdd = () => {
    setFormData({
      MaDichVu: '',
      MaNhaId: '',
      TenDichVu: '',
      LoaiDichVu: '',
      DonGia: '',
      DonViTinh: '',
      TenNha: ''
    })
    setErrors({})
    setEditIndex(null)
    setOpen(true)
  }

  const handleOpenEdit = (row, index) => {
    setFormData(row)
    setErrors({})
    setEditIndex(index)
    setOpen(true)
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
    const newValue = value || ''
    if (formData[name] !== newValue) {
      setFormData(prev => ({ ...prev, [name]: newValue }))
      if (newValue.trim() !== '') {
        setErrors(prev => ({ ...prev, [name]: '' }))
      }
    }
  }


  const validateForm = () => {
    const requiredFields = ['MaDichVu', 'TenNha', 'TenDichVu', 'LoaiDichVu', 'DonGia', 'DonViTinh']
    const newErrors = {}
    requiredFields.forEach(field => {
      if (!formData[field] || formData[field].toString().trim() === '') {
        newErrors[field] = 'Thông tin bắt buộc'
      }
    })
    setErrors(newErrors)
    return Object.keys(newErrors).length === 0
  }


  const handleSubmit = async () => {
    if (!validateForm()) return

    try {
      setLoading(true)
      if (editIndex === null) {
        // Create new service
        await phiDichVuService.create(formData)
        setSnackbar({
          open: true,
          message: 'Thêm phí dịch vụ thành công',
          severity: 'success'
        })
      } else {
        // Update existing service
        await phiDichVuService.update(formData.MaDichVu, formData)
        setSnackbar({
          open: true,
          message: 'Cập nhật phí dịch vụ thành công',
          severity: 'success'
        })
      }
      await fetchPhiDichVu()
      setOpen(false)
    } catch (error) {
      console.error('Lỗi khi lưu phí dịch vụ:', error)
      setSnackbar({
        open: true,
        message: 'Không thể lưu phí dịch vụ',
        severity: 'error'
      })
    } finally {
      setLoading(false)
    }
  }

  const handleDeleteConfirm = (row) => {
    confirm({
      title: 'Xóa dịch vụ',
      description: `Bạn chắc chắn muốn xóa dịch vụ ${row.MaDichVu}?`,
      confirmationText: 'Xóa',
      cancellationText: 'Hủy'
    }).then(() => {
      handleDelete(row.MaDichVu)
    }).catch(() => { })
  }

  const filteredRows = rows.filter(row => {
    if (!row) return false;
    
    const matchSearch = searchText.trim() === '' || (
      (row.MaDichVu && row.MaDichVu.toLowerCase().includes(searchText.toLowerCase())) ||
      (row.TenDichVu && row.TenDichVu.toLowerCase().includes(searchText.toLowerCase()))
    )

    const matchToaNha = !filterToaNha || row.TenNha === filterToaNha
    const matchLoai = !filterLoai || row.LoaiDichVu === filterLoai

    return matchSearch && matchToaNha && matchLoai
  })

  const listToaNha = [...new Set(toaNhas.map(p => p.TenNha))].map(nha => ({ title: nha }))
  const listLoaiDichVu = [
    { title: 'Tiền điện' },
    { title: 'Tiền nước' },
    { title: 'Tiền vệ sinh' },
    { title: 'Tiền internet' },
    { title: 'Tiền phí quản lý' },
    { title: 'Tiền gửi xe' },
    { title: 'Tiền phí giặt sấy' }
  ]
  const listDonViTinh = [
    { title: 'Người' },
    { title: 'Phòng' },
    { title: 'Kwh' },
    { title: 'm³' },
    { title: 'm²' },
    { title: 'Xe' },
    { title: 'Lượt/Lần' },
    { title: 'Kg' },
    { title: 'Giờ' }
  ]

  return (
    <Box sx={{ m: 1 }}>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <h1 style={{ margin: 0 }}>Quản lý dịch vụ</h1>
        <Box sx={{ display: 'flex', gap: 2 }}>
          <Tooltip title="Thêm dịch vụ">
            <Button variant="contained" onClick={handleOpenAdd} sx={{ bgcolor: '#248F55' }}><AddIcon /></Button>
          </Tooltip>
          <Tooltip title="Xuất báo cáo">
            <Button variant="contained" sx={{ bgcolor: '#28C76F' }}><SaveAltIcon /></Button>
          </Tooltip>
        </Box>
      </Box>

      <Box sx={{ display: 'flex', gap: 2, mt: 1 }}>
        <Autocomplete
          options={listToaNha}
          getOptionLabel={(option) => option.title}
          onChange={(e, value) => {
            const tenNha = value?.title || null
            setSelectedTenNha(tenNha)
            setFilterToaNha(tenNha)
          }}
          sx={{
            width: '33.3333333333%',
            '& .MuiInputBase-root': {
              paddingTop: 0,
              paddingBottom: 0
            },
            '& .MuiFormLabel-root': {
              top: -7.5 // tuỳ chỉnh nếu label bị lệch
            }
          }}
          renderInput={(params) => <TextField {...params} label="Tòa nhà" variant="outlined" />}
          clearOnEscape
        />
        <Autocomplete
          options={listLoaiDichVu}
          getOptionLabel={(option) => option.title}
          onChange={(e, value) => setFilterLoai(value?.title || null)}
          sx={{
            width: '33.3333333333%',
            '& .MuiInputBase-root': {
              paddingTop: 0,
              paddingBottom: 0
            },
            '& .MuiFormLabel-root': {
              top: -7.5 // tuỳ chỉnh nếu label bị lệch
            }
          }}
          renderInput={(params) => <TextField {...params} label="Loại dịch vụ" />}
          clearOnEscape
        />

        {/* Tìm kiếm */}
        <TextField
          sx={{
            width: '33.3333333333%',
            '& .toolpad-demo-app-x0zmg8-MuiInputBase-input-MuiOutlinedInput-input': {
              py: '7.5px'
            },
            '& .MuiFormLabel-root': {
              top: -5
            }
          }}
          placeholder="Tìm kiếm theo mã, tên dịch vụ"
          value={searchText}
          onChange={(e) => setSearchText(e.target.value)}
          InputProps={{
            startAdornment: (
              <SearchIcon fontSize='small' sx={{ mr: 1 }} />
            )
          }}
        />
      </Box>


      {/* Dialog Thêm/Sửa */}
      <Dialog open={open} onClose={handleClose} maxWidth="md" fullWidth>
        <DialogTitle sx={{ bgcolor: '#EEEEEE', py: 1 }}>{editIndex === null ? 'Thêm dịch vụ' : 'Sửa dịch vụ'}</DialogTitle>
        <DialogContent sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
          <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
            <Grid item xs={4} sx={{ width: 'calc(520px/3)', mt: 2 }}>
              <TextField
                label="Mã dịch vụ (*)"
                fullWidth
                name="MaDichVu"
                value={formData.MaDichVu}
                onChange={handleChange}
                disabled={editIndex !== null}
                error={!!errors.MaDichVu}
                helperText={errors.MaDichVu}
              />
            </Grid>
            <Grid container spacing={2} sx={{ gap: 1 }}>
              <Grid item xs={4} sx={{ width: 'calc(544px/2)' }}>
                <TextField
                  label="Tên dịch vụ (*)"
                  fullWidth
                  name="TenDichVu"
                  value={formData.TenDichVu}
                  onChange={handleChange}
                  error={!!errors.TenDichVu}
                  helperText={errors.TenDichVu}
                />
              </Grid>
              <Grid item xs={4} sx={{ width: 'calc(544px/2)' }}>
                <Autocomplete
                  disablePortal={false}
                  PopperProps={{
                    disablePortal: false,
                  }}
                  options={listLoaiDichVu}
                  getOptionLabel={(option) => option.title}
                  value={listLoaiDichVu.find(t => t.title === formData.LoaiDichVu) || null}
                  onChange={(e, value) => handleAutoChange('LoaiDichVu', value?.title || '')}
                  renderInput={(params) => (
                    <TextField
                      {...params}
                      label="Loại dịch vụ (*)"
                      error={!!errors.LoaiDichVu}
                      helperText={errors.LoaiDichVu}
                    />
                  )}
                />
              </Grid>
            </Grid>

            <Grid container spacing={2} sx={{ gap: 1 }}>
              <Grid item xs={6} sx={{ width: 'calc(544px/2)' }}>
                <TextField
                  label="Đơn giá (*)"
                  fullWidth
                  name="DonGia"
                  value={formData.DonGia}
                  onChange={handleChange}
                  error={!!errors.DonGia}
                  helperText={errors.DonGia}
                />
              </Grid>
              <Grid item xs={6} sx={{ width: 'calc(544px/2)' }}>
                <Autocomplete
                  disablePortal={false}
                  PopperProps={{
                    disablePortal: false,
                  }}
                  options={listDonViTinh}
                  getOptionLabel={(option) => option.title}
                  value={listDonViTinh.find(t => t.title === formData.DonViTinh) || null}
                  onChange={(e, value) => handleAutoChange('DonViTinh', value?.title || '')}
                  renderInput={(params) => (
                    <TextField
                      {...params}
                      label="Đơn vị tính (*)"
                      error={!!errors.DonViTinh}
                      helperText={errors.DonViTinh}
                    />
                  )}
                />
              </Grid>
            </Grid>
            <Grid item xs={4} sx={{ width: '100%' }}>
              <Autocomplete
                disablePortal
                options={listToaNha}
                getOptionLabel={(option) => option.title}
                value={listToaNha.find(t => t.title === formData.TenNha) || null}
                onChange={(e, value) => handleAutoChange('TenNha', value?.title || '')}
                renderInput={(params) => (
                  <TextField
                    {...params}
                    label="Tòa nhà sử dụng (*)"
                    error={!!errors.TenNha}
                    helperText={errors.TenNha}
                  />
                )}
              />
            </Grid>
          </Box>
        </DialogContent>


        <DialogActions>
          <Button onClick={handleClose}>Hủy</Button>
          <Button onClick={handleSubmit} variant="contained">Lưu</Button>
        </DialogActions>
      </Dialog >

      {!loading && (
        <TableContainer component={Paper} sx={{ marginTop: '16px' }}>
          <Table sx={{ minWidth: 700 }} aria-label="customized table">
            <TableHead>
              <TableRow>
                <StyledTableCell>Mã dịch vụ</StyledTableCell>
                <StyledTableCell>Tên dịch vụ</StyledTableCell>
                <StyledTableCell>Loại dịch vụ</StyledTableCell>
                <StyledTableCell align='right'>Giá tiền</StyledTableCell>
                <StyledTableCell align='center'>Thao tác</StyledTableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {filteredRows.length === 0 ? (
                <TableRow>
                  <StyledTableCell colSpan={5} align="center">Không có dữ liệu</StyledTableCell>
                </TableRow>
              ) : (
                filteredRows.map((row, index) => (
                  <StyledTableRow key={row.MaDichVu}>
                    <StyledTableCell sx={{ p: '8px' }}>{row.MaDichVu}</StyledTableCell>
                    <StyledTableCell sx={{ p: '8px' }}>
                      {row.TenDichVu}
                      <Box sx={{ color: '#B9B9C3' }}>Tòa nhà: {row.TenNha}</Box>
                    </StyledTableCell>
                    <StyledTableCell sx={{ p: '8px' }}>{row.LoaiDichVu}</StyledTableCell>
                    <StyledTableCell align='right' sx={{ p: '8px' }}>{formatCurrency(row.DonGia)}/{row.DonViTinh}</StyledTableCell>
                    <StyledTableCell sx={{ p: '8px' }}>
                      <Box sx={{ display: 'flex', gap: 1, justifyContent: 'center' }}>
                        <Tooltip title="Sửa">
                          <Button
                            variant="contained"
                            sx={{ bgcolor: '#828688' }}
                            onClick={() => handleOpenEdit(row, index)}
                          >
                            <BorderColorIcon fontSize='small' />
                          </Button>
                        </Tooltip>
                        <Tooltip title="Xóa">
                          <Button
                            variant="contained"
                            sx={{ bgcolor: '#EA5455' }}
                            onClick={() => handleDeleteConfirm(row)}
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

export default PhiDichVu
