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
  const [rows, setRows] = React.useState(ToaNhaData || [])
  const [open, setOpen] = React.useState(false)
  const [formData, setFormData] = React.useState({
    id: '', TenNha: '', SoPhong: '', DiaChiNha: '', TrangThai: 'Hoạt động',
    TinhThanh: '', QuanHuyen: '', XaPhuong: ''
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
    setFormData(row)
    setErrors({})
    setEditId(row.id)
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
      id: '',
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

  const handleDelete = (id) => {
    setRows(rows.filter(row => row.id !== id))
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
    const requiredFields = ['id', 'TenNha', 'SoPhong', 'DiaChiNha', 'TinhThanh', 'QuanHuyen', 'XaPhuong']
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
    if (!validateForm()) return

    const newData = { ...formData }

    if (editId === null) {
      // Thêm mới: kiểm tra trùng id có thể thêm nếu muốn
      setRows(prev => [...prev, newData])
    } else {
      // Sửa: tìm index trong rows bằng id rồi cập nhật
      setRows(prev => {
        const index = prev.findIndex(r => r.id === editId)
        if (index === -1) return prev
        const updated = [...prev]
        updated[index] = newData
        return updated
      })
    }
    setOpen(false)
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
        row.id.toLowerCase().includes(keyword) ||
        row.DiaChiNha.toLowerCase().includes(keyword)
      return matchesStatus && matchesSearch
    })
    : []

  return (
    <Box sx={{ m: 1 }}>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <h1 style={{ margin: 0 }}>Quản lý tòa nhà</h1>
        <Box sx={{
          display: 'flex',
          gap: 2,
          '&:hover': {

          }
        }}>
          <Tooltip title="Thêm tòa nhà">
            <Button variant="contained" onClick={handleOpenAdd} sx={{ bgcolor: '#248F55' }}><AddIcon /></Button>
          </Tooltip>
          <Tooltip title="Xuất báo cáo">
            <Button variant="contained" sx={{ bgcolor: '#28C76F' }}><SaveAltIcon /></Button>
          </Tooltip>
        </Box>
      </Box>

      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mt: 1 }}>
        <Autocomplete
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
        />

        <TextField
          sx={{
            width: '45%',
            '& .toolpad-demo-app-x0zmg8-MuiInputBase-input-MuiOutlinedInput-input': {
              py: '7.5px'
            },
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
                  value={formData.id || ''}
                  name="id"
                  onChange={handleChange}
                />
              </Grid>
            </Grid>
          </Box>

          <Box>
            <strong>Thông tin địa chỉ</strong>
            <Grid container spacing={2} mt={1} sx={{ display: 'flex', flexDirection: 'column' }}>
              <Box sx={{ display: 'flex', gap: 1, justifyContent: 'space-between  ' }}>
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
              <StyledTableCell>Mã tòa nhà</StyledTableCell>
              <StyledTableCell>Tên tòa nhà</StyledTableCell>
              <StyledTableCell>Số phòng</StyledTableCell>
              <StyledTableCell>Địa chỉ</StyledTableCell>
              <StyledTableCell>Trạng thái</StyledTableCell>
              <StyledTableCell align='center'>Tháo tác</StyledTableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {(filteredRows || []).map((row) => (
              <StyledTableRow key={row.id}>
                <StyledTableCell sx={{ p: '8px' }}>{row.id}</StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>{row.TenNha}</StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>{Array.isArray(row.Phongs) ? row.Phongs.length : 0}</StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>
                  {`${row.DiaChiNha}, ${row.XaPhuong}, ${row.QuanHuyen}, ${row.TinhThanh}`}
                </StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>{row.TrangThai}</StyledTableCell>
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
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </Box >
  )
}

export default ToaNha
