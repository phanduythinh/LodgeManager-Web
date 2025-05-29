import React, { useState, useEffect } from 'react'
import { phongService, toaNhaService } from '~/apis/services'
import {
  Table, TableBody, TableContainer, TableHead, TableRow, Paper, Button, Box, TextField, Dialog, DialogActions,
  DialogContent, DialogTitle, Grid, CircularProgress, Typography
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
  const [editIndex, setEditIndex] = useState(null)
  const [filterToaNha, setFilterToaNha] = useState(null)
  const [filterTang, setFilterTang] = useState(null)
  const [filterTrangThai, setFilterTrangThai] = useState(null)
  const [searchText, setSearchText] = useState('')
  const confirm = useConfirm()

  useEffect(() => {
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
        console.log('API response:', response)
        
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
      } finally {
        setLoading(false)
      }
    }
    
    fetchData()
  }, [])

  const handleDelete = (MaPhong) => {
    setRooms(rooms.filter(room => room.MaPhong !== MaPhong))
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
    if (!validateForm()) return

    try {
      setLoading(true)
      
      // Chuẩn bị dữ liệu trước khi gửi
      // Đảm bảo các trường số được chuyển từ chuỗi sang số
      const preparedData = {
        ...formData,
        GiaThue: formData.GiaThue ? Number(formData.GiaThue) : 0,
        DatCoc: formData.DatCoc ? Number(formData.DatCoc) : 0,
        DienTich: formData.DienTich ? Number(formData.DienTich) : 0,
        SoKhachToiDa: formData.SoKhachToiDa ? Number(formData.SoKhachToiDa) : 0
      }
      
      // Kiểm tra trùng mã phòng khi thêm mới
      if (editIndex === null) {
        const isDuplicate = rooms.some(r => r.MaPhong === preparedData.MaPhong);
        if (isDuplicate) {
          setErrors(prev => ({
            ...prev,
            MaPhong: 'Mã phòng đã tồn tại'
          }));
          setLoading(false);
          return;
        }
      }

      // Xử lý thêm mới hoặc cập nhật
      if (editIndex === null) {
        // Thêm mới
        try {
          // Gọi API
          const response = await phongService.create(preparedData);
          console.log('Kết quả thêm phòng:', response);
          
          // Lấy lại danh sách phòng
          const updatedRoomsResponse = await phongService.getAll();
          const updatedRooms = Array.isArray(updatedRoomsResponse) ? updatedRoomsResponse : 
                             (updatedRoomsResponse && updatedRoomsResponse.data) ? updatedRoomsResponse.data : [];
          
          setRooms(updatedRooms);
          setOpen(false);
          alert('Thêm phòng thành công!');
        } catch (apiError) {
          console.error('Lỗi khi gọi API thêm phòng:', apiError);
          
          // Kiểm tra nếu là môi trường phát triển hoặc API không hoạt động
          if (process.env.NODE_ENV === 'development') {
            // Fallback: thêm trực tiếp vào state nếu API lỗi
            setRooms(prev => [...prev, preparedData]);
            setOpen(false);
            alert('Thêm phòng thành công (chỉ lưu trên giao diện)!');
          } else {
            throw apiError; // Ném lỗi để xử lý ở catch bên ngoài
          }
        }
      } else {
        // Cập nhật
        try {
          // Gọi API
          const response = await phongService.update(preparedData.MaPhong, preparedData);
          console.log('Kết quả cập nhật phòng:', response);
          
          // Lấy lại danh sách phòng
          const updatedRoomsResponse = await phongService.getAll();
          const updatedRooms = Array.isArray(updatedRoomsResponse) ? updatedRoomsResponse : 
                             (updatedRoomsResponse && updatedRoomsResponse.data) ? updatedRoomsResponse.data : [];
          
          setRooms(updatedRooms);
          setOpen(false);
          alert('Cập nhật phòng thành công!');
        } catch (apiError) {
          console.error('Lỗi khi gọi API cập nhật phòng:', apiError);
          
          // Kiểm tra nếu là môi trường phát triển hoặc API không hoạt động
          if (process.env.NODE_ENV === 'development') {
            // Fallback: cập nhật trực tiếp vào state nếu API lỗi
            const updatedRooms = [...rooms];
            updatedRooms[editIndex] = preparedData;
            setRooms(updatedRooms);
            setOpen(false);
            alert('Cập nhật phòng thành công (chỉ lưu trên giao diện)!');
          } else {
            throw apiError; // Ném lỗi để xử lý ở catch bên ngoài
          }
        }
      }
    } catch (error) {
      console.error('Lỗi khi lưu phòng:', error);
      // Hiển thị thông tin lỗi chi tiết hơn
      let errorMessage = 'Có lỗi xảy ra khi lưu phòng. ';
      
      if (error.response) {
        // Lỗi từ server với mã trạng thái
        errorMessage += `Mã lỗi: ${error.response.status}. `;
        if (error.response.data && error.response.data.message) {
          errorMessage += error.response.data.message;
        }
      } else if (error.request) {
        // Lỗi không nhận được phản hồi từ server
        errorMessage += 'Không thể kết nối đến server. Kiểm tra kết nối mạng của bạn.';
      } else {
        // Lỗi khác
        errorMessage += error.message || 'Lỗi không xác định.';
      }
      
      alert(errorMessage);
    } finally {
      setLoading(false);
    }
  }

  const hanhdleDeletePhong = (row) => {
    confirm({
      title: 'Xóa phòng',
      description: `Bạn chắc chắn muốn xóa phòng ${row.TenPhong}?`,
      confirmationText: 'Xóa',
      cancellationText: 'Hủy',
      confirmationButtonProps: { variant: 'contained', color: 'error' },
      cancellationButtonProps: { variant: 'outlined' }
    }).then(async () => {
      try {
        setLoading(true)
        await phongService.delete(row.MaPhong)
        handleDelete(row.MaPhong)
      } catch (error) {
        console.error('Lỗi khi xóa phòng:', error)
      } finally {
        setLoading(false)
      }
    }).catch(() => { })
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

  // Lấy danh sách trạng thái từ rooms
  const getStatuses = () => {
    const statuses = [...new Set(rooms.map(room => room.TrangThai))]
    return statuses.map(status => ({ title: status }))
  }

  // Lấy danh sách tầng từ rooms
  const getFloors = () => {
    const floors = [...new Set(rooms.map(room => room.Tang))]
    return floors.map(floor => ({ title: floor }))
  }

  // Lấy danh sách tòa nhà
  const getBuildingNames = () => {
    return buildings.map(building => ({ title: building.TenNha }))
  }
  
  if (loading) {
    return (
      <Box sx={{ display: 'flex', justifyContent: 'center', mt: 3 }}>
        <CircularProgress />
      </Box>
    )
  }
  
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
          options={getFloors()}
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
          options={getStatuses()}
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
                disabled={editIndex !== null}
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
                  options={getFloors()}
                  getOptionLabel={(option) => option.title}
                  value={getFloors().find(t => t.title === formData.Tang) || null}
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
                  options={getStatuses()}
                  getOptionLabel={(option) => option.title}
                  value={getStatuses().find(t => t.title === formData.TrangThai) || null}
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
          <Button onClick={handleClose}>Hủy</Button>
          <Button onClick={handleSubmit} variant="contained">Lưu</Button>
        </DialogActions>
      </Dialog>

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
                  <StyledTableCell sx={{ p: '8px' }}>{room.MaPhong}</StyledTableCell>
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
    </Box>
  )
}

export default Phong
