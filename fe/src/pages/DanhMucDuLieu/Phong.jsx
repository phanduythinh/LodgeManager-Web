import React, { useState, useEffect } from 'react'
import { phongService, toaNhaService } from '~/apis/services'
import {
  Table, TableBody, TableContainer, TableHead, TableRow, Paper, Button, Box, TextField, Dialog, DialogActions,
  DialogContent, DialogTitle, Grid, CircularProgress, Snackbar, Alert
} from '@mui/material'
import Autocomplete from '@mui/material/Autocomplete'
import AddIcon from '@mui/icons-material/Add'
import SaveAltIcon from '@mui/icons-material/SaveAlt'
import Tooltip from '@mui/material/Tooltip'
import DeleteIcon from '@mui/icons-material/Delete'
import BorderColorIcon from '@mui/icons-material/BorderColor'
import SearchIcon from '@mui/icons-material/Search'
import { useConfirm } from 'material-ui-confirm'
import { StyledTableCell, StyledTableRow } from '~/components/StyledTable'
import { formatCurrency } from '~/components/formatCurrency'

function Phong() {
  const [rooms, setRooms] = useState([])
  const [buildings, setBuildings] = useState([])
  const [loading, setLoading] = useState(true)
  const [open, setOpen] = useState(false)
  const [formData, setFormData] = useState({
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
  const [errors, setErrors] = useState({})
  const [editId, setEditId] = useState(null)
  const [filterToaNha, setFilterToaNha] = useState(null)
  const [filterTang, setFilterTang] = useState(null)
  const [filterTrangThai, setFilterTrangThai] = useState(null)
  const [searchText, setSearchText] = useState('')
  const confirm = useConfirm()
  const [snackbar, setSnackbar] = useState({
    open: false,
    message: '',
    severity: 'success'
  })

  const fetchData = async () => {
    try {
      setLoading(true)
      // Lấy danh sách tòa nhà
      const buildingsResponse = await toaNhaService.getAll()
      const buildingsData = Array.isArray(buildingsResponse) ? buildingsResponse :
        (buildingsResponse && buildingsResponse.data) ? buildingsResponse.data : []
      setBuildings(buildingsData)

      // Lấy danh sách phòng
      const response = await phongService.getAll()

      // Xử lý dữ liệu
      let data = []
      if (Array.isArray(response)) {
        data = response
      } else if (response && Array.isArray(response.data)) {
        data = response.data
      }

      setRooms(data)
    } catch (error) {
      console.error('Lỗi khi lấy danh sách phòng:', error)
      setSnackbar({ open: true, message: `Lỗi khi tải dữ liệu: ${error.message}`, severity: 'error' })
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchData()
  }, [])

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
    setEditId(null)
    setOpen(true)
  }

  const handleOpenEdit = (row) => {
    setFormData(row)
    setErrors({})
    setEditId(row.id) // Sử dụng id thay vì MaPhong
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
    const requiredFields = ['MaPhong', 'TenNha', 'TenPhong', 'Tang', 'GiaThue', 'DatCoc', 'DienTich', 'SoKhachToiDa', 'TrangThai']
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
    if (!validateForm()) return;

    setLoading(true);
    try {
      const preparedData = {
        ...formData,
        GiaThue: Number(formData.GiaThue) || 0,
        DatCoc: Number(formData.DatCoc) || 0,
        DienTich: Number(formData.DienTich) || 0,
        SoKhachToiDa: Number(formData.SoKhachToiDa) || 0,
      };

      if (editId) {
        // Sửa
        await phongService.update(editId, preparedData);
        setSnackbar({ open: true, message: 'Cập nhật phòng thành công!', severity: 'success' });
      } else {
        // Thêm
        const isDuplicate = rooms.some(r => r.MaPhong === preparedData.MaPhong);
        if (isDuplicate) {
          setErrors(prev => ({ ...prev, MaPhong: 'Mã phòng đã tồn tại' }));
          setLoading(false);
          return;
        }
        await phongService.create(preparedData);
        setSnackbar({ open: true, message: 'Thêm phòng thành công!', severity: 'success' });
      }

      setOpen(false);
      setEditId(null);
      await fetchData();
    } catch (error) {
      console.error('Lỗi khi lưu phòng:', error);
      const errorMessage = error.response?.data?.message || error.message || 'Có lỗi xảy ra.';
      setSnackbar({ open: true, message: `Lỗi: ${errorMessage}`, severity: 'error' });
    } finally {
      setLoading(false);
    }
  }

  const hanhdleDeletePhong = (row) => {
    confirm({
      title: 'Xác nhận xóa',
      description: `Bạn có chắc chắn muốn xóa phòng ${row.TenPhong}?`,
      confirmationText: 'Xóa',
      cancellationText: 'Hủy'
    })
      .then(async () => {
        try {
          setLoading(true);
          // Luôn sử dụng id để xóa để đảm bảo tính nhất quán
          const response = await phongService.delete(row.id)
          setSnackbar({ open: true, message: 'Xóa phòng thành công!', severity: 'success' })
          await fetchData();
        } catch (error) {
          console.error('Lỗi khi xóa phòng:', error)
          const errorMessage = error.response?.data?.message || error.message || 'Có lỗi xảy ra.';
          setSnackbar({ open: true, message: `Lỗi khi xóa phòng: ${errorMessage}`, severity: 'error' })
        } finally {
            setLoading(false);
        }
      })
      .catch(() => {
        // Người dùng đã hủy
      })
  }

  // Filter rows theo filterToaNha, filterTang, filterTrangThai và searchText
  const filteredRooms = rooms.filter(room => {
    if (filterToaNha && room.TenNha !== filterToaNha) return false
    if (filterTang && room.Tang !== filterTang) return false
    if (filterTrangThai && room.TrangThai !== filterTrangThai) return false
    if (searchText.trim() !== '') {
      const text = searchText.toLowerCase()
      if (!(
        (room.MaPhong && room.MaPhong.toLowerCase().includes(text)) ||
        (room.TenPhong && room.TenPhong.toLowerCase().includes(text)) ||
        (room.TenNha && room.TenNha.toLowerCase().includes(text))
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

  // Lấy danh sách trạng thái từ rooms
  // const getStatuses = () => {
  //   const statuses = [...new Set(rooms.map(room => room.TrangThai))]
  //   return statuses.map(status => ({ title: status }))
  // }

  // Lấy danh sách tầng từ rooms
  // const getFloors = () => {
  //   const floors = [...new Set(rooms.map(room => room.Tang))]
  //   return floors.map(floor => ({ title: floor }))
  // }

  // Lấy danh sách tòa nhà
  const getBuildingNames = () => {
    return buildings.map(building => ({ title: building.TenNha }))
  }

  // if (loading) {
  //   return (
  //     <Box sx={{ display: 'flex', justifyContent: 'center', mt: 3 }}>
  //       <CircularProgress />
  //     </Box>
  //   )
  // }

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
          options={getBuildingNames()}
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
        <DialogTitle sx={{ bgcolor: '#EEEEEE', py: 1 }}>{editId === null ? 'Thêm phòng' : 'Sửa phòng'}</DialogTitle>
        <DialogContent sx={{ display: 'flex', flexDirection: 'column', gap: 2, mt: 1 }}>
          <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
            <Grid item xs={4} sx={{ width: 'calc(520px/3)' }}>
              <TextField
                label="Mã phòng (*)"
                fullWidth
                name="MaPhong"
                value={formData.MaPhong}
                onChange={handleChange}
                disabled={editId !== null}
                error={!!errors.MaPhong}
                helperText={errors.MaPhong}
              />
            </Grid>
            <Grid container spacing={2} sx={{ gap: 1 }}>
              <Grid item xs={4} sx={{ width: 'calc(520px/3)' }}>
                <Autocomplete
                  disablePortal
                  options={getBuildingNames()}
                  getOptionLabel={(option) => option.title}
                  value={getBuildingNames().find(t => t.title === formData.TenNha) || null}
                  onChange={(e, value) => handleAutoChange('TenNha', value?.title || '')}
                  renderInput={(params) => (
                    <TextField
                      {...params}
                      label="Tòa nhà (*)"
                      error={!!errors.TenNha}
                      helperText={errors.TenNha}
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
              <Grid item xs={4} sx={{ width: 'calc(520px/3)' }}>
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

            <Grid container spacing={2} sx={{ gap: 1 }}>
              <Grid item xs={4} sx={{ width: 'calc(520px/3)' }}>
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
              <Grid item xs={4} sx={{ width: 'calc(520px/3)' }}>
                <Autocomplete
                  disablePortal
                  options={listTrangThai}
                  getOptionLabel={(option) => option.title}
                  value={listTrangThai.find(t => t.title === formData.TrangThai) || null}
                  onChange={(e, value) => handleAutoChange('TrangThai', value?.title || '')}
                  renderInput={(params) => (
                    <TextField
                      {...params}
                      label="Trạng thái thuê (*)"
                      error={!!errors.TrangThai}
                      helperText={errors.TrangThai}
                    />
                  )}
                />
              </Grid>
            </Grid>
          </Box>
        </DialogContent>

        <DialogActions>
          <Button onClick={handleClose} disabled={loading}>Hủy</Button>
          <Button onClick={handleSubmit} variant="contained" disabled={loading}>
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
                <StyledTableCell>Mã phòng</StyledTableCell>
                <StyledTableCell>Tên phòng</StyledTableCell>
                <StyledTableCell align='right'>Giá thuê</StyledTableCell>
                <StyledTableCell align='right'>Đặt cọc</StyledTableCell>
                <StyledTableCell align='right'>Diện tích</StyledTableCell>
                <StyledTableCell align='right'>Số khách</StyledTableCell>
                <StyledTableCell align='center'>Trạng thái</StyledTableCell>
                <StyledTableCell align='center'>Thao tác</StyledTableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {filteredRooms.length === 0 ? (
                <TableRow>
                  <StyledTableCell colSpan={8} align="center">Không có dữ liệu</StyledTableCell>
                </TableRow>
              ) : (
                filteredRooms.map((room, index) => (
                  <StyledTableRow key={room.MaPhong}>
                    <StyledTableCell sx={{ p: '8px' }}>
                      {room.MaPhong}
                      <Box sx={{ color: '#B9B9C3' }}>ID: {room.id}</Box>
                    </StyledTableCell>
                    <StyledTableCell sx={{ p: '8px' }}>
                      <Box>{room.TenPhong}</Box>
                      <Box sx={{ color: '#B9B9C3' }}>Tòa nhà: {room.TenNha}</Box>
                      <Box sx={{ color: '#B9B9C3' }}>{room.Tang}</Box>
                    </StyledTableCell>
                    <StyledTableCell align='right' sx={{ p: '8px' }}>{formatCurrency(room.GiaThue)}</StyledTableCell>
                    <StyledTableCell align='right' sx={{ p: '8px' }}>{formatCurrency(room.DatCoc)}</StyledTableCell>
                    <StyledTableCell align='right' sx={{ p: '8px' }}>{room.DienTich} m²</StyledTableCell>
                    <StyledTableCell align='right' sx={{ p: '8px' }}>{room.SoKhachToiDa}</StyledTableCell>
                    <StyledTableCell align='center' sx={{ p: '8px' }}>
                      <span
                        style={{
                          padding: '4px 8px',
                          borderRadius: '12px',
                          color: room.TrangThai === 'Đang ở' ? '#388e3c' : '#EA5455',
                          backgroundColor: room.TrangThai === 'Đang ở' ? '#c8e6c9' : '#EA54551F',
                          fontWeight: 600,
                          fontSize: '13px',
                          display: 'inline-block',
                          textAlign: 'center',
                          minWidth: '80px'
                        }}
                      >
                        {room.TrangThai}
                      </span>
                    </StyledTableCell>
                    <StyledTableCell sx={{ p: '8px' }}>
                      <Box sx={{ display: 'flex', gap: 1, justifyContent: 'center' }}>
                        <Tooltip title="Sửa">
                          <Button
                            variant="contained"
                            sx={{ bgcolor: '#828688' }}
                            onClick={() => handleOpenEdit(room, index)}
                          >
                            <BorderColorIcon fontSize='small' />
                          </Button>
                        </Tooltip>
                        <Tooltip title="Xóa">
                          <Button
                            variant="contained"
                            sx={{ bgcolor: '#EA5455' }}
                            onClick={() => hanhdleDeletePhong(room)}
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

export default Phong
