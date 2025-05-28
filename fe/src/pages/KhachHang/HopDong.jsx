import { useState, useMemo, useEffect } from 'react'
import { StyledTableCell, StyledTableRow } from '~/components/StyledTable'
import {
  Table, TableBody, TableCell, TableContainer,
  TableHead, TableRow, Paper, Button, Box, TextField, Dialog, DialogActions,
  DialogContent, DialogTitle, Grid, CircularProgress, Alert, Snackbar
} from '@mui/material'
import Autocomplete from '@mui/material/Autocomplete'
import Popper from '@mui/material/Popper'
import { Checkbox } from '@mui/material'
import AddIcon from '@mui/icons-material/Add'
import SaveAltIcon from '@mui/icons-material/SaveAlt'
import Tooltip from '@mui/material/Tooltip'
import DeleteIcon from '@mui/icons-material/Delete'
import BorderColorIcon from '@mui/icons-material/BorderColor'
import SearchIcon from '@mui/icons-material/Search'
import PersonAddAltIcon from '@mui/icons-material/PersonAddAlt'
import AddCircleOutlineIcon from '@mui/icons-material/AddCircleOutline'

import { useConfirm } from 'material-ui-confirm'
import { hopDongService, toaNhaService, phongService, khachHangService, phiDichVuService } from '~/apis/services'
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs'
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider'
import { DatePicker } from '@mui/x-date-pickers/DatePicker'
import dayjs from 'dayjs'
import { formatCurrency } from '~/components/formatCurrency'

function HopDong() {
  const [rows, setRows] = useState([])
  const [listToaNha, setListToaNha] = useState([]);
  const [listPhong, setListPhong] = useState([]);
  const [loading, setLoading] = useState(false);
  const [snackbar, setSnackbar] = useState({
    open: false,
    message: '',
    severity: 'success'
  });
  
  // Thêm useEffect để tải dữ liệu khi component mount
  useEffect(() => {
    fetchHopDong();
    fetchToaNha();
    fetchKhachHang();
  }, []);
  
  // Hàm tải dữ liệu hợp đồng từ API
  const fetchHopDong = async () => {
    try {
      setLoading(true);
      const response = await hopDongService.getAll();
      console.log('HopDong API response:', response); // Log toàn bộ response để debug
      
      // Xử lý cả hai trường hợp: mảng trực tiếp hoặc object có thuộc tính data
      let data = [];
      if (Array.isArray(response)) {
        data = response;
      } else if (response && Array.isArray(response.data)) {
        data = response.data;
      }
      
      // Kiểm tra xem mỗi hợp đồng có KhachHangs không, nếu không thì gán mảng rỗng
      data = data.map(item => ({
        ...item,
        KhachHangs: Array.isArray(item.KhachHangs) ? item.KhachHangs : [],
        DichVus: Array.isArray(item.DichVus) ? item.DichVus : []
      }));
      
      console.log('HopDong processed data:', data); // Log dữ liệu đã xử lý
      setRows(data);
    } catch (error) {
      console.error('Lỗi khi lấy danh sách hợp đồng:', error);
      setSnackbar({
        open: true,
        message: 'Không thể tải danh sách hợp đồng',
        severity: 'error'
      });
    } finally {
      setLoading(false);
    }
  };
  
  // Hàm tải danh sách tòa nhà từ API
  const fetchToaNha = async () => {
    try {
      const response = await toaNhaService.getAll();
      const data = Array.isArray(response) ? response : 
                  (response && Array.isArray(response.data)) ? response.data : [];
      setListToaNha(data);
    } catch (error) {
      console.error('Lỗi khi lấy danh sách tòa nhà:', error);
    }
  };
  
  // Hàm tải danh sách khách hàng từ API
  const fetchKhachHang = async () => {
    try {
      const response = await khachHangService.getAll();
      const data = Array.isArray(response) ? response : 
                  (response && Array.isArray(response.data)) ? response.data : [];
      setDanhSachKhachHang(data);
    } catch (error) {
      console.error('Lỗi khi lấy danh sách khách hàng:', error);
    }
  };

  const [open, setOpen] = useState(false)
  const [formData, setFormData] = useState({
    MaHopDong: '', TenNha: '', TenPhong: '', NgayBatDau: '', NgayKetThuc: '', TienThue: '', TienCoc: '', ChuKyThanhToan: '', NgayTinhTien: '', KhachHangs: [], DichVus: [], TrangThai: ''
  })
  const [errors, setErrors] = useState({})
  const [editId, setEditId] = useState(null)
  const [filterStatus, setFilterStatus] = useState(null)
  const [searchKeyword, setSearchKeyword] = useState('')
  const [filteredRows, setFilteredRows] = useState([]);

  useEffect(() => {
    setFilteredRows(rows);
  }, [rows]);

  const [openKhachHangDialog, setOpenKhachHangDialog] = useState(false)
  const [selectedKhachHangs, setSelectedKhachHangs] = useState([])
  const [khachHangDuocChon, setKhachHangDuocChon] = useState([])
  const [danhSachKhachHang, setDanhSachKhachHang] = useState([])

  const [openDichVuDialog, setOpenDichVuDialog] = useState(false)
  const [selectedDichVus, setSelectedDichVus] = useState([])
  const [dichVuDuocChon, setDichVuDuocChon] = useState([])

  //Các component thêm khách hàng vào hợp đồng
  const handleOpenAddKhachHang = () => {
    setOpenKhachHangDialog(true)
  }
  const handleCloseAddKhachHang = () => {
    setOpenKhachHangDialog(false)
  }
  // Handler chọn/bỏ chọn 1 khách hàng
  const handleToggleKhachHang = (maKhachHang) => {
    setSelectedKhachHangs(prev =>
      prev.includes(maKhachHang)
        ? prev.filter(id => id !== maKhachHang)
        : [...prev, maKhachHang]
    )
  }
  // Handler chọn/bỏ chọn tất cả
  // Kiểm tra chọn bỏ chọn hết chưa
  const isAllSelected = selectedKhachHangs.length === danhSachKhachHang.length
  const isIndeterminate = selectedKhachHangs.length > 0 && !isAllSelected
  const handleSelectAll = () => {
    if (isAllSelected) {
      setSelectedKhachHangs([]);
    } else {
      setSelectedKhachHangs(danhSachKhachHang.map(kh => kh.MaKhachHang))
    }
  }
  const handleAddKhachHang = () => {
    const khachDuocThem = danhSachKhachHang.filter(kh => selectedKhachHangs.includes(kh.MaKhachHang));
    setKhachHangDuocChon(khachDuocThem);
    setFormData(prev => ({
      ...prev,
      KhachHangs: khachDuocThem.map(kh => kh.MaKhachHang)
    }));
    setOpenKhachHangDialog(false);
  };

  //Các component thêm dịch vụ vào hợp đồng
  const handleOpenAddDichVu = () => {
    setOpenDichVuDialog(true)
  }
  const handleCloseAddDichVu = () => {
    setOpenDichVuDialog(false)
  }
  // Lấy danh sách dịch vụ từ tòa nhà được chọn
  const [dichVusTuToaNha, setDichVusTuToaNha] = useState([])
  
  // Fetch dịch vụ của tòa nhà khi MaNhaId thay đổi
  useEffect(() => {
    if (formData.MaNhaId) {
      fetchDichVuByToaNha(formData.MaNhaId);
    }
  }, [formData.MaNhaId])
  
  const fetchDichVuByToaNha = async (maNha) => {
    try {
      const response = await phiDichVuService.getAll();
      // Lọc dịch vụ theo tòa nhà
      const dichVuCuaToaNha = response.data.filter(dv => dv.MaNhaId === maNha);
      setDichVusTuToaNha(dichVuCuaToaNha);
    } catch (error) {
      console.error('Lỗi khi lấy danh sách dịch vụ:', error);
    }
  }
  // Logic chọn/bỏ chọn từng dịch vụ
  const handleToggleDichVu = (maDichVu) => {
    setSelectedDichVus(prev =>
      prev.includes(maDichVu)
        ? prev.filter(id => id !== maDichVu)
        : [...prev, maDichVu]
    );
  };
  // Chọn / Bỏ chọn tất cả dịch vụ
  const isAllDichVuSelected = selectedDichVus.length === dichVusTuToaNha.length;
  const isDichVuIndeterminate = selectedDichVus.length > 0 && !isAllDichVuSelected;

  const handleSelectAllDichVus = () => {
    if (isAllDichVuSelected) {
      setSelectedDichVus([]);
    } else {
      setSelectedDichVus(dichVusTuToaNha.map(dv => dv.MaDichVu));
    }
  };
  // Xác nhận và thêm dịch vụ đã chọn vào hợp đồng
  const handleAddDichVu = () => {
    const dichVuThem = dichVusTuToaNha.filter(dv => selectedDichVus.includes(dv.MaDichVu));
    setDichVuDuocChon(dichVuThem);
    setFormData(prev => ({
      ...prev,
      DichVus: dichVuThem.map(dv => dv.MaDichVu)
    }));
    setOpenDichVuDialog(false);
  };

  const handleOpenEdit = (row) => {
    const selectedToaNha = ToaNhaData.find(tn => tn.TenNha === row.TenNha);
    const initialPhongs = selectedToaNha ? selectedToaNha.Phongs : [];
    setListPhong(initialPhongs);

    setFormData({
      MaHopDong: row.MaHopDong || '',
      MaNhaId: selectedToaNha?.MaNha || '',
      MaPhongId: initialPhongs.find(p => p.TenPhong === row.TenPhong)?.MaPhong || '',
      TenNha: row.TenNha || '',
      TenPhong: row.TenPhong || '',
      NgayBatDau: row.NgayBatDau ? dayjs(row.NgayBatDau, 'DD/MM/YYYY') : null,
      NgayKetThuc: row.NgayKetThuc ? dayjs(row.NgayKetThuc, 'DD/MM/YYYY') : null,
      NgayTinhTien: row.NgayTinhTien ? dayjs(row.NgayTinhTien, 'DD/MM/YYYY') : null,
      TienThue: row.TienThue || '',
      TienCoc: row.TienCoc || '',
      ChuKyThanhToan: row.ChuKyThanhToan || '',
      KhachHangs: row.KhachHangs || [],
      DichVus: row.DichVus || [],
      TrangThai: row.TrangThai || ''
    });

    setKhachHangDuocChon(row.KhachHangs || []); // Khách hàng đã có trong hợp đồng
    setSelectedKhachHangs(row.KhachHangs.map(kh => kh.MaKhachHang) || []); // Lấy MaKhachHang cho dialog chọn
    setDichVuDuocChon(row.DichVus || []); // Dịch vụ đã có trong hợp đồng
    setSelectedDichVus(row.DichVus.map(dv => dv.MaDichVu) || []); // Lấy MaDichVu cho dialog chọn
    setEditId(row.MaHopDong)
    setErrors({})
    setOpen(true)
  };


  const handleOpenAdd = () => {
    setOpen(true)
    setEditId(null)
    setFormData({
      MaHopDong: '',
      MaNhaId: '',
      MaPhongId: '',
      TenNha: '',
      TenPhong: '',
      NgayBatDau: '',
      NgayKetThuc: '',
      TienThue: '',
      TienCoc: '',
      ChuKyThanhToan: '',
      NgayTinhTien: '',
      KhachHangs: [],
      DichVus: [],
      TrangThai: ''
    })
  }

  const fetchPhongByToaNha = async (maNha) => {
    try {
      const response = await toaNhaService.getPhongs(maNha);
      setListPhong(response.data);
    } catch (error) {
      console.error('Lỗi khi lấy danh sách phòng:', error);
    }
  };

  const handleDelete = async (id) => {
    try {
      setLoading(true);
      await hopDongService.delete(id);
      await fetchHopDong();
      setSnackbar({
        open: true,
        message: 'Xóa hợp đồng thành công',
        severity: 'success'
      });
    } catch (error) {
      console.error('Lỗi khi xóa hợp đồng:', error);
      setSnackbar({
        open: true,
        message: 'Không thể xóa hợp đồng',
        severity: 'error'
      });
    } finally {
      setLoading(false);
    }
  }

  // Trong phần thêm hợp đồng
  const handleRemoveKhachHang = (maKhachHang) => {
    setKhachHangDuocChon(prev => {
      const updated = prev.filter(kh => kh.MaKhachHang !== maKhachHang)
      setFormData(prevForm => ({ ...prevForm, KhachHangs: updated.map(kh => kh.MaKhachHang) }))
      return updated
    });
  };

  // Trong phần thêm dịch vụ
  const handleRemoveDichVu = (maDichVu) => {
    setDichVuDuocChon(prev => prev.filter(dv => dv.MaDichVu !== maDichVu));
  };

  const handleClose = () => {
    setOpen(false) // đóng dialog
    setFormData({}) // reset form
    setErrors({})
    setKhachHangDuocChon([]) // reset khách hàng được chọn
    setDichVuDuocChon([]) // reset dịch vụ được chọn
    setSelectedKhachHangs([])
    setSelectedDichVus([])
    setEditId(null) // nếu bạn đang ở chế độ sửa thì cũng reset luôn
    setListPhong([])
  }

  const handleChange = (e) => {
    const { name, value } = e.target
    setFormData(prev => ({ ...prev, [name]: value }))
    if (value.trim() !== '') {
      setErrors(prev => ({ ...prev, [name]: '' }))
    }
  }

  const handleAutoChange = (field, value) => {
    if (field === 'TenNha') {
      // Khi chọn tòa nhà, lấy MaNhaId và reset TenPhong
      const toaNha = listToaNha.find(tn => tn.TenNha === value)
      const maNhaId = toaNha?.MaNha || ''

      // Lấy danh sách phòng của tòa nhà từ API
      if (maNhaId) {
        fetchPhongByToaNha(maNhaId);
      } else {
        setListPhong([]);
      }

      setFormData(prev => ({
        ...prev,
        TenNha: value,
        MaNhaId: maNhaId,
        TenPhong: '',
        MaPhongId: ''
      }))
    } else if (field === 'TenPhong') {
      // Khi chọn phòng, lấy MaPhongId và GiaThue
      const phong = listPhong.find(p => p.TenPhong === value)
      const maPhongId = phong?.MaPhong || ''
      const giaThue = phong?.GiaThue || ''
      const datCoc = phong?.DatCoc || ''

      setFormData(prev => ({
        ...prev,
        TenPhong: value,
        MaPhongId: maPhongId,
        TienThue: giaThue,
        TienCoc: datCoc
      }))
    } else {
      setFormData(prev => ({ ...prev, [field]: value }))
    }
  };

  const validateForm = () => {
    const requiredFields = ['MaHopDong', 'NgayBatDau', 'NgayKetThuc', 'TienThue', 'NgayTinhTien', 'MaNhaId', 'MaPhongId', 'ChuKyThanhToan']
    const newErrors = {}

    requiredFields.forEach(field => {
      if (!formData[field] ||
        (typeof formData[field] === 'string' && formData[field].trim() === '') ||
        (Array.isArray(formData[field]) && formData[field].length === 0)) {
        newErrors[field] = 'Thông tin bắt buộc';
      }
    })
    if (khachHangDuocChon.length === 0) {
      newErrors.KhachHangs = 'Cần thêm ít nhất một khách hàng';
    }
    if (dichVuDuocChon.length === 0) {
      newErrors.DichVus = 'Cần thêm ít nhất một dịch vụ';
    }

    setErrors(newErrors)
    return Object.keys(newErrors).length === 0
  }

  const handleSubmit = () => {
    if (!validateForm()) return;

    const selectedToaNha = listToaNha.find(n => n.MaNha === formData.MaNhaId)
    const selectedPhong = listPhong.find(p => p.MaPhong === formData.MaPhongId)

    const newData = {
      ...formData,
      TenNha: selectedToaNha ? selectedToaNha.TenNha : '',
      MaNhaId: selectedToaNha ? selectedToaNha.MaNha : '', // Đảm bảo lưu MaNhaId
      TenPhong: selectedPhong ? selectedPhong.TenPhong : '',
      MaPhongId: selectedPhong ? selectedPhong.MaPhong : '', // Đảm bảo lưu MaPhongId
      KhachHangs: khachHangDuocChon, // Lưu toàn bộ đối tượng khách hàng
      DichVus: dichVuDuocChon.map(dv => ({ // Lưu toàn bộ đối tượng dịch vụ với các trường MaCongTo, ChiSoDau, NgayTinhPhi
        ...dv,
        MaCongTo: dv.MaCongTo || '',
        ChiSoDau: dv.ChiSoDau || '',
        NgayTinhPhi: dv.NgayTinhPhi // Dùng định dạng đã có hoặc format lại nếu cần
      })),
      NgayBatDau: formData.NgayBatDau && formData.NgayBatDau.format
        ? formData.NgayBatDau.format('DD/MM/YYYY')
        : formData.NgayBatDau,
      NgayKetThuc: formData.NgayKetThuc && formData.NgayKetThuc.format
        ? formData.NgayKetThuc.format('DD/MM/YYYY')
        : formData.NgayKetThuc,
      NgayTinhTien: formData.NgayTinhTien && formData.NgayTinhTien.format
        ? formData.NgayTinhTien.format('DD/MM/YYYY')
        : formData.NgayTinhTien,
      TrangThai: 'Còn hạn' // Mặc định trạng thái là 'Còn hạn' khi thêm hoặc sửa
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
    handleClose(); // Gọi handleClose để reset toàn bộ form và state
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
  const handleSearch = () => {
    const keyword = searchKeyword.toLowerCase();
    const filtered = rows.filter((row) => {
      const matchesStatus = filterStatus ? row.TrangThai === filterStatus : true;
      const matchesSearch =
        (row.MaHopDong?.toLowerCase().includes(keyword) || false) ||
        (row.TenNha?.toLowerCase().includes(keyword) || false) || // Tìm kiếm theo tên tòa nhà
        (row.TenPhong?.toLowerCase().includes(keyword) || false) || // Tìm kiếm theo tên phòng
        (row.KhachHangs[0]?.HoTen?.toLowerCase().includes(keyword) || false) || // Tìm kiếm theo tên khách hàng đại diện
        (row.TrangThai?.toLowerCase().includes(keyword) || false);
      return matchesStatus && matchesSearch;
    });
    setFilteredRows(filtered);
  };

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
        <Button variant="contained" onClick={handleSearch}>Tìm kiếm</Button>
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

          <Grid container spacing={2} sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
              <strong>2. Khách hàng</strong>
              <Tooltip title="Thêm khách hàng">
                <Button variant="contained" onClick={handleOpenAddKhachHang} sx={{ bgcolor: '#248F55' }}>
                  <PersonAddAltIcon />
                </Button>
              </Tooltip>
            </Box>
            <TableContainer component={Paper}>
              <Table sx={{ maxWidth: 600 }} aria-label="customized table">
                <TableHead>
                  <TableRow>
                    <StyledTableCell>Tên cư dân</StyledTableCell>
                    <StyledTableCell>Số điện thoại</StyledTableCell>
                    <StyledTableCell>CMND/CCCD</StyledTableCell>
                    <StyledTableCell align='center' sx={{ width: '100px' }}>Tháo tác</StyledTableCell>
                  </TableRow>
                </TableHead>
                <TableBody>
                  {khachHangDuocChon.map((kh) => (
                    <TableRow key={kh.MaKhachHang}>
                      <StyledTableCell>{kh.HoTen}</StyledTableCell>
                      <StyledTableCell>{kh.SoDienThoai}</StyledTableCell>
                      <StyledTableCell>{kh.CCCD}</StyledTableCell>
                      <StyledTableCell align='center'>
                        <Tooltip title="Xóa">
                          <Button
                            variant="contained"
                            sx={{ bgcolor: '#EA5455' }}
                            onClick={() => handleRemoveKhachHang(kh.MaKhachHang)}
                          >
                            <DeleteIcon fontSize='small' />
                          </Button>
                        </Tooltip>
                      </StyledTableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </TableContainer>
          </Grid>

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
                    disablePortal={false}
                    PopperComponent={(props) => <Popper {...props} disablePortal={false} />}
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

          <Grid container spacing={2} sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
              <strong>4. Tiền phí dịch vụ</strong>
              <Tooltip title="Thêm dịch vụ">
                <Button variant="contained" onClick={handleOpenAddDichVu} sx={{ bgcolor: '#248F55' }}>
                  <AddCircleOutlineIcon />
                </Button>
              </Tooltip>
            </Box>
            <TableContainer component={Paper}>
              <Table sx={{ minWidth: 700 }} aria-label="customized table">
                <TableHead>
                  <TableRow>
                    <StyledTableCell>Tên dịch vụ</StyledTableCell>
                    <StyledTableCell align='right'>Giá tiền</StyledTableCell>
                    <StyledTableCell>Công tơ</StyledTableCell>
                    <StyledTableCell>Chỉ số đầu</StyledTableCell>
                    <StyledTableCell>Ngày tính phí</StyledTableCell>
                    <StyledTableCell align='center'>Thao tác</StyledTableCell>
                  </TableRow>
                </TableHead>
                <TableBody>
                  {dichVuDuocChon.map((dv) => (
                    <TableRow key={dv.MaDichVu}>
                      <StyledTableCell>{dv.TenDichVu}</StyledTableCell>
                      <StyledTableCell>{dv.DonGia}/{dv.DonViTinh}</StyledTableCell>
                      <StyledTableCell>
                        <TextField
                          size="small"
                          variant="outlined"
                          value={dv.MaCongTo || ''}
                          onChange={(e) => {
                            const newValue = e.target.value;
                            setDichVuDuocChon((prev) =>
                              prev.map((item) =>
                                item.MaDichVu === dv.MaDichVu ? { ...item, MaCongTo: newValue } : item
                              )
                            );
                          }}
                          placeholder="Nhập mã công tơ"
                        />
                      </StyledTableCell>
                      <StyledTableCell>
                        <TextField
                          size="small"
                          variant="outlined"
                          type="number"
                          value={dv.ChiSoDau || ''}
                          onChange={(e) => {
                            const newValue = e.target.value;
                            setDichVuDuocChon((prev) =>
                              prev.map((item) =>
                                item.MaDichVu === dv.MaDichVu ? { ...item, ChiSoDau: newValue } : item
                              )
                            );
                          }}
                          placeholder="Chỉ số đầu"
                        />
                      </StyledTableCell>
                      <StyledTableCell>
                        <LocalizationProvider dateAdapter={AdapterDayjs}>
                          <DatePicker
                            sx={{
                              width: '140px',
                              '& .MuiPickersSectionList-root': { py: '8.5px' }
                            }}
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
                      </StyledTableCell>
                      <StyledTableCell align='center'>
                        <Tooltip title="Xóa">
                          <Button
                            variant="contained"
                            sx={{ bgcolor: '#EA5455' }}
                            onClick={() => handleRemoveDichVu(dv.MaDichVu)}
                          >
                            <DeleteIcon fontSize='small' />
                          </Button>
                        </Tooltip>
                      </StyledTableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </TableContainer>
          </Grid>
        </DialogContent >

        <DialogActions>
          <Button onClick={handleClose}>Hủy</Button>
          <Button onClick={handleSubmit} variant="contained">Lưu</Button>
        </DialogActions>
      </Dialog >

      {/* Thêm khách hàng vào hợp đồng */}
      <Dialog open={openKhachHangDialog} onClose={handleCloseAddKhachHang} maxWidth="sm" fullWidth>
        <DialogTitle>Danh sách khách hàng</DialogTitle>
        <DialogContent component={Paper}>
          <Table size="small" aria-label="Danh sách khách hàng"
            sx={{
              border: '1px solid #ccc',
              '& th, & td': {
                border: '1px solid #ccc',
              },
              '& thead th': {
                backgroundColor: '#f5f5f5',
              },
            }}
          >
            <TableHead>
              <TableRow>
                <TableCell padding="checkbox">
                  <Checkbox
                    checked={isAllSelected}
                    indeterminate={isIndeterminate}
                    onChange={handleSelectAll}
                  />
                </TableCell>
                <TableCell>Họ tên</TableCell>
                <TableCell>Số điện thoại</TableCell>
                <TableCell>CMND/CCCD</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {danhSachKhachHang.map((kh) => (
                <TableRow key={kh.MaKhachHang}>
                  <TableCell padding="checkbox">
                    <Checkbox
                      checked={selectedKhachHangs.includes(kh.MaKhachHang)}
                      onChange={() => handleToggleKhachHang(kh.MaKhachHang)}
                    />
                  </TableCell>
                  <TableCell>{kh.HoTen}</TableCell>
                  <TableCell>{kh.SoDienThoai}</TableCell>
                  <TableCell>{kh.CCCD}</TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </DialogContent>
        <DialogActions>
          <Button onClick={handleCloseAddKhachHang}>Hủy</Button>
          <Button onClick={handleAddKhachHang}>Chọn</Button>
        </DialogActions>
      </Dialog>

      {/* Thêm dịch vụ vào hợp đồng */}
      <Dialog open={openDichVuDialog} onClose={handleCloseAddDichVu} maxWidth="md" fullWidth>
        <DialogTitle>Danh sách dịch vụ</DialogTitle>
        <DialogContent component={Paper}>
          <Table size="small" aria-label="Danh sách khách hàng"
            sx={{
              border: '1px solid #ccc',
              '& th, & td': {
                border: '1px solid #ccc',
              },
              '& thead th': {
                backgroundColor: '#f5f5f5',
              },
            }}
          >
            <TableHead>
              <TableRow>
                <TableCell padding="checkbox">
                  <Checkbox
                    checked={isAllDichVuSelected}
                    indeterminate={isDichVuIndeterminate}
                    onChange={handleSelectAllDichVus}
                  />
                </TableCell>
                <TableCell>Mã dịch vụ</TableCell>
                <TableCell>Tên dịch vụ</TableCell>
                <TableCell>Loại dịch vụ</TableCell>
                <TableCell align='right'>Giá tiền</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {dichVusTuToaNha.map((dv) => (
                <StyledTableRow key={dv.MaDichVu}>
                  <TableCell padding="checkbox">
                    <Checkbox
                      checked={selectedDichVus.includes(dv.MaDichVu)}
                      onChange={() => handleToggleDichVu(dv.MaDichVu)}
                    />
                  </TableCell>
                  <TableCell sx={{ p: '8px' }}>{dv.MaDichVu}</TableCell>
                  <TableCell sx={{ p: '8px' }}>{dv.TenDichVu}</TableCell>
                  <TableCell sx={{ p: '8px' }}>{dv.LoaiDichVu}</TableCell>
                  <TableCell align='right' sx={{ p: '8px' }}>{dv.DonGia}/{dv.DonViTinh}</TableCell>
                </StyledTableRow>
              ))}
            </TableBody>
          </Table>
        </DialogContent>
        <DialogActions>
          <Button onClick={handleCloseAddDichVu}>Hủy</Button>
          <Button onClick={handleAddDichVu}>Chọn</Button>
        </DialogActions>
      </Dialog>

      {/* Bảng */}
      <TableContainer component={Paper} sx={{ marginTop: '16px' }}>
        <Table sx={{ minWidth: 700 }} aria-label="Danh sách hợp đồng">
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
                  <Box>{row.KhachHangs && row.KhachHangs.length > 0 ? row.KhachHangs[0].HoTen : 'Không có'}</Box>
                  <Box sx={{ color: '#B9B9C3' }}>Tòa nhà: {row.TenNha || 'Không có'}</Box>
                  <Box sx={{ color: '#B9B9C3' }}>{row.TenPhong || 'Không có'}</Box>
                </StyledTableCell>
                <StyledTableCell sx={{ p: '8px' }}>{formatCurrency(row.TienThue)}</StyledTableCell>
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

export default HopDong
