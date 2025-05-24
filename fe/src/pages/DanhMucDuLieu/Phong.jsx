import * as React from 'react'
import { styled } from '@mui/material/styles'
import {
  Table, TableBody, TableCell, tableCellClasses, TableContainer,
  TableHead, TableRow, Paper, Button, Box, TextField, Dialog, DialogActions,
  DialogContent, DialogTitle, Grid
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

function ToaNha() {
  const [rows, setRows] = React.useState(() =>
    ToaNhaData.flatMap(toaNha =>
      toaNha.Phongs.map(phong => ({
        ...phong,
        TenNha: toaNha.TenNha
      }))
    )
  )
  const [open, setOpen] = React.useState(false)
  const [formData, setFormData] = React.useState({
    MaPhong: '',
    TenNha: '',
    TenPhong: '',
    Tang: '',
    GiaThue: '',
    DatCoc: '',
    DienTich: '',
    SoKhachToiDa: '',
    TrangThai: 'Còn trống'
  })
  const [errors, setErrors] = React.useState({})
  const [editIndex, setEditIndex] = React.useState(null)
  const [filterToaNha, setFilterToaNha] = React.useState(null)
  const [filterTang, setFilterTang] = React.useState(null)
  const [filterTrangThai, setFilterTrangThai] = React.useState(null)
  const [searchText, setSearchText] = React.useState('')
  const confirm = useConfirm()

  const handleDelete = (MaPhong) => {
    setRows(rows.filter(row => row.MaPhong !== MaPhong))
  }

  const handleOpenAdd = () => {
    setFormData({
      MaPhong: '',
      TenNha: '',
      TenPhong: '',
      Tang: '',
      GiaThue: '',
      DatCoc: '',
      DienTich: '',
      SoKhachToiDa: '',
      TrangThai: 'Còn trống'
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
    setFormData(prev => ({ ...prev, [name]: value || '' }))
    if ((value || '').trim() !== '') {
      setErrors(prev => ({ ...prev, [name]: '' }))
    }
  }


  const validateForm = () => {
    const requiredFields = ['MaPhong', 'MaNhaId', 'TenPhong', 'Tang', 'GiaThue', 'DatCoc', 'DienTich', 'SoKhachToiDa']
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

  const hanhdleDeletePhong = (row) => {
    confirm({
      title: 'Xóa phòng',
      description: `Bạn chắc chắn muốn xóa phòng ${row.MaPhong}?`,
      confirmationText: 'Xóa',
      cancellationText: 'Hủy'
    }).then(() => {
      handleDelete(row.MaPhong)
    }).catch(() => { })
  }

  // Filter rows theo filterToaNha, filterTang, filterTrangThai và searchText
  const filteredRows = rows.filter(row => {
    if (filterToaNha && row.TenNha !== filterToaNha) return false
    if (filterTang && row.Tang !== filterTang) return false
    if (filterTrangThai && row.TrangThai !== filterTrangThai) return false
    if (searchText.trim() !== '') {
      const text = searchText.toLowerCase()
      if (!(
        row.MaPhong.toLowerCase().includes(text) ||
        row.TenPhong.toLowerCase().includes(text) ||
        row.TenNha.toLowerCase().includes(text)
      )) return false
    }
    return true
  })

  const listTrangThai = [{ title: 'Đang ở' }, { title: 'Còn trống' }]
  const listTang = [
    { title: 'Tầng 1' },
    { title: 'Tầng 2' },
    { title: 'Tầng 3' },
    { title: 'Tầng 4' },
    { title: 'Tầng 5' },
    { title: 'Tầng 6' },
    { title: 'Tầng 7' },
    { title: 'Tầng 8' },
    { title: 'Tầng 9' },
    { title: 'Tầng 10' }
  ]
  const listToaNha = [...new Set(ToaNhaData.map(p => p.TenNha))].map(nha => ({ title: nha }))

  return (
    <Box sx={{ m: 1 }}>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <h1 style={{ margin: 0 }}>Quản lý phòng</h1>
        <Box sx={{ display: 'flex', gap: 2 }}>
          <Tooltip title="Thêm phòng">
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
          onChange={(e, value) => setFilterToaNha(value?.title || null)}
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
          renderInput={(params) => <TextField {...params} label="Tòa nhà" />}
          clearOnEscape
        />
        <Autocomplete
          options={listTang}
          getOptionLabel={(option) => option.title}
          onChange={(e, value) => setFilterTang(value?.title || null)}
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
          renderInput={(params) => <TextField {...params} label="Tầng" />}
          clearOnEscape
        />
        <Autocomplete
          options={listTrangThai}
          getOptionLabel={(option) => option.title}
          onChange={(e, value) => setFilterTrangThai(value?.title || null)}
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
          renderInput={(params) => <TextField {...params} label="Trạng thái thuê" />}
          clearOnEscape
        />
      </Box>

      <TextField
        sx={{
          mt: 1, width: '100%',
          '& .toolpad-demo-app-x0zmg8-MuiInputBase-input-MuiOutlinedInput-input': {
            py: '7.5px'
          },
          '& .MuiFormLabel-root': {
            top: -5
          }
        }}
        placeholder="Tìm kiếm theo mã phòng, tên phòng, tên nhà"
        value={searchText}
        onChange={(e) => setSearchText(e.target.value)}
        InputProps={{
          startAdornment: (
            <SearchIcon fontSize='small' sx={{ mr: 1 }} />
          )
        }}
      />

      {/* Dialog Thêm/Sửa */}
      <Dialog open={open} onClose={handleClose} maxWidth="md" fullWidth>
        <DialogTitle sx={{ bgcolor: '#EEEEEE', py: 1 }}>{editIndex === null ? 'Thêm phòng' : 'Sửa phòng'}</DialogTitle>
        <DialogContent sx={{ display: 'flex', flexDirection: 'column', gap: 2, mt: 1 }}>
          <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
            <Grid item xs={4} sx={{ width: 'calc(520px/3)' }}>
              <TextField
                label="Mã phòng (*)"
                fullWidth
                name="MaPhong"
                value={formData.MaPhong}
                onChange={handleChange}
                error={!!errors.MaPhong}
                helperText={errors.MaPhong}
              />
            </Grid>
            <Grid container spacing={2} sx={{ gap: 1 }}>
              <Grid item xs={4} sx={{ width: 'calc(520px/3)' }}>
                <Autocomplete
                  disablePortal
                  options={listToaNha}
                  getOptionLabel={(option) => option.title}
                  value={listToaNha.find(t => t.title === formData.MaNhaId) || null}
                  onChange={(e, value) => handleAutoChange('MaNhaId', value?.title || '')}
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
              <Grid item xs={4} sx={{ width: 'calc(520px/3)' }}>
                <TextField
                  label="Tên phòng (*)"
                  fullWidth
                  name="TenPhong"
                  value={formData.TenPhong}
                  onChange={handleChange}
                  error={!!errors.TenPhong}
                  helperText={errors.TenPhong}
                />
              </Grid>
              <Grid item xs={4} sx={{ width: 'calc(520px/3)' }}>
                <Autocomplete
                  disablePortal
                  options={listTang}
                  getOptionLabel={(option) => option.title}
                  value={listTang.find(t => t.title === formData.Tang) || null}
                  onChange={(e, value) => handleAutoChange('Tang', value?.title || '')}
                  renderInput={(params) => (
                    <TextField
                      {...params}
                      label="Tầng (*)"
                      error={!!errors.Tang}
                      helperText={errors.Tang}
                    />
                  )}
                />
              </Grid>
            </Grid>

            <Grid container spacing={2} sx={{ gap: 1 }}>

              <Grid item xs={4} sx={{ width: 'calc(520px/3)' }}>
                <TextField
                  label="Giá thuê (*)"
                  fullWidth
                  name="GiaThue"
                  value={formData.GiaThue}
                  onChange={handleChange}
                  error={!!errors.GiaThue}
                  helperText={errors.GiaThue}
                />
              </Grid>
              <Grid item xs={4} sx={{ width: 'calc(520px/3)' }}>
                <TextField
                  label="Đặt cọc (*)"
                  fullWidth
                  name="DatCoc"
                  value={formData.DatCoc}
                  onChange={handleChange}
                  error={!!errors.DatCoc}
                  helperText={errors.DatCoc}
                />
              </Grid>
              <Grid container spacing={2} sx={{ width: 'calc(520px/3)' }}>
                <Grid item xs={4}>
                  <TextField
                    label="Diện tích (m²) (*)"
                    fullWidth
                    name="DienTich"
                    value={formData.DienTich}
                    onChange={handleChange}
                    error={!!errors.DienTich}
                    helperText={errors.DienTich}
                  />
                </Grid>
              </Grid>
              <Grid item xs={4} >
              </Grid>
              <TextField
                label="Số khách tối đa (*)"
                fullWidth
                name="SoKhachToiDa"
                value={formData.SoKhachToiDa}
                onChange={handleChange}
                error={!!errors.SoKhachToiDa}
                helperText={errors.SoKhachToiDa}
              />
            </Grid>
            <Grid item xs={4}>
              <Autocomplete
                disablePortal
                options={listTrangThai}
                getOptionLabel={(option) => option.title}
                value={listTrangThai.find(t => t.title === formData.TrangThai) || null}
                onChange={(e, value) => handleAutoChange('TrangThai', value?.title || '')}
                renderInput={(params) => (
                  <TextField
                    {...params}
                    label="Trạng thái (*)"
                    error={!!errors.TrangThai}
                    helperText={errors.TrangThai}
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
              <StyledTableCell>Mã phòng</StyledTableCell>
              <StyledTableCell>Tên phòng</StyledTableCell>
              <StyledTableCell align='right'>Giá thuê</StyledTableCell>
              <StyledTableCell align='right'>Đặt cọc</StyledTableCell>
              <StyledTableCell align='right'>Diện tích</StyledTableCell>
              <StyledTableCell align='right'>Số khách</StyledTableCell>
              <StyledTableCell align='center'>Trạng thái</StyledTableCell>
              <StyledTableCell align='center'>Tháo tác</StyledTableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {filteredRows.map((row, index) => (
              <StyledTableRow key={row.MaPhong}>
                <StyledTableCell sx={{ p: '8px' }}>{row.MaPhong}</StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>
                  <Box>{row.TenPhong}</Box>
                  <Box sx={{ color: '#B9B9C3' }}>Tòa nhà: {row.TenNha}</Box>
                  <Box sx={{ color: '#B9B9C3' }}>{row.Tang}</Box>
                </StyledTableCell>
                <StyledTableCell align='right' sx={{ p: '8px' }}>{row.GiaThue}</StyledTableCell>
                <StyledTableCell align='right' sx={{ p: '8px' }}>{row.DatCoc}</StyledTableCell>
                <StyledTableCell align='right' sx={{ p: '8px' }}>{row.DienTich} m²</StyledTableCell>
                <StyledTableCell align='right' sx={{ p: '8px' }}>{row.SoKhachToiDa}</StyledTableCell>
                <StyledTableCell align='center' sx={{ p: '8px' }}>
                  <span
                    style={{
                      padding: '4px 8px',
                      borderRadius: '12px',
                      color: row.TrangThai === 'Đang ở' ? '#388e3c' : '#EA5455',
                      backgroundColor: row.TrangThai === 'Đang ở' ? '#c8e6c9' : '#EA54551F',
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
                        onClick={() => handleOpenEdit(row, index)}
                      >
                        <BorderColorIcon fontSize='small' />
                      </Button>
                    </Tooltip>
                    <Tooltip title="Xóa">
                      <Button
                        variant="contained"
                        sx={{ bgcolor: '#EA5455' }}
                        onClick={() => hanhdleDeletePhong(row)}
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

export default ToaNha
