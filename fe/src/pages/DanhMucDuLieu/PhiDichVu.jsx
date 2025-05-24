import { useState } from 'react'
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
import { ToaNhaData } from '../../apis/mock-data'
import { useConfirm } from 'material-ui-confirm'

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

function PhiDichVu() {
  const [rows, setRows] = useState(() =>
    ToaNhaData.flatMap(toaNha =>
      toaNha.PhiDichVus.map(phiDichVu => ({
        ...phiDichVu,
        TenNha: toaNha.TenNha
      }))
    )
  )
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

  const handleDelete = (MaDichVu) => {
    setRows(rows.filter(row => row.MaDichVu !== MaDichVu))
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


  const handleSubmit = () => {
    if (!validateForm()) return

    if (editIndex === null) {
      setRows(prev => [...prev, formData])
    } else {
      const updated = [...rows]
      updated[editIndex] = formData
      setRows(updated)
    }
    setOpen(false)
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
    if (filterToaNha && row.TenNha !== filterToaNha) return false
    if (filterLoai && row.LoaiDichVu !== filterLoai) return false
    if (searchText.trim() !== '') {
      const text = searchText.toLowerCase()
      if (!(
        row.MaDichVu.toLowerCase().includes(text) ||
        row.TenDichVu.toLowerCase().includes(text)
      )) return false
    }
    return true
  })

  const listToaNha = [...new Set(ToaNhaData.map(p => p.TenNha))].map(nha => ({ title: nha }))
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
            {filteredRows.map((row, index) => (
              <StyledTableRow key={row.MaDichVu}>
                <StyledTableCell sx={{ p: '8px' }}>{row.MaDichVu}</StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>
                  {row.TenDichVu}
                  <Box sx={{ display: 'none' }}>{row.TenNha}</Box>
                </StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>{row.LoaiDichVu}</StyledTableCell>
                <StyledTableCell align='right' sx={{ p: '8px' }}>{row.DonGia}/{row.DonViTinh}</StyledTableCell>
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
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </Box >
  )
}

export default PhiDichVu
