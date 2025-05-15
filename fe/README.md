# React + Vite

This template provides a minimal setup to get React working in Vite with HMR and some ESLint rules.

Currently, two official plugins are available:

- [@vitejs/plugin-react](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react) uses [Babel](https://babeljs.io/) for Fast Refresh
- [@vitejs/plugin-react-swc](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react-swc) uses [SWC](https://swc.rs/) for Fast Refresh

## Expanding the ESLint configuration

If you are developing a production application, we recommend using TypeScript with type-aware lint rules enabled. Check out the [TS template](https://github.com/vitejs/vite/tree/main/packages/create-vite/template-react-ts) for information on how to integrate TypeScript and [`typescript-eslint`](https://typescript-eslint.io) in your project.

<!-- import { Routes, Route, Link } from 'react-router-dom'
import ManageCustomers from './pages/ManageCustomers'
import ContractManagement from './pages/ContractManagement'
import HostelManagement from './pages/HostelManagement'
import InvoiceManagement from './pages/InvoiceManagement'
import ServiceManagement from './pages/ServiceManagement'
import ViewStatistics from './pages/ViewStatistics'

import AppBar from '@mui/material/AppBar'
import Box from '@mui/material/Box'
import Toolbar from '@mui/material/Toolbar'
import Typography from '@mui/material/Typography'
import Button from '@mui/material/Button'
import IconButton from '@mui/material/IconButton'
import MenuIcon from '@mui/icons-material/Menu'

function App() {

  return (
    <Box sx={{ flexGrow: 1 }}>
      <AppBar position="static">
        <Toolbar>
          <IconButton
            size="large"
            edge="start"
            color="inherit"
            aria-label="menu"
            sx={{ mr: 2 }}
          >
            <MenuIcon />
          </IconButton>
          <Typography variant="h6" component={Link} to="/Bao_cao_thong_ke" sx={{ flexGrow: 1, color: 'inherit', textDecoration: 'none' }}>
            Báo cáo thông kê
          </Typography>
          <Typography variant="h6" component={Link} to="/Quan_ly_khach_hang" sx={{ flexGrow: 1, color: 'inherit', textDecoration: 'none' }}>
            Quản lý khách hàng
          </Typography>
          <Typography variant="h6" component={Link} to="/Quan_ly_nha_tro" sx={{ flexGrow: 1, color: 'inherit', textDecoration: 'none' }}>
            Quản lý nhà trọ
          </Typography>
          <Typography variant="h6" component={Link} to="/Quan_ly_hoa_don" sx={{ flexGrow: 1, color: 'inherit', textDecoration: 'none' }}>
            Quản lý hóa đơn
          </Typography>
          <Typography variant="h6" component={Link} to="/Quan_ly_dich_vu" sx={{ flexGrow: 1, color: 'inherit', textDecoration: 'none' }}>
            Quản lý dịch vụ
          </Typography>
          <Typography variant="h6" component={Link} to="/Quan_ly_hop_dong" sx={{ flexGrow: 1, color: 'inherit', textDecoration: 'none' }}>
            Quản lý hợp đồng
          </Typography>
          <Button color="inherit">Login</Button>
        </Toolbar>
      </AppBar>

      <Routes>
        <Route path='/Bao_cao_thong_ke' element={<ViewStatistics />} />
        <Route path='/Quan_ly_khach_hang' element={<ManageCustomers />} />
        <Route path='/Quan_ly_nha_tro' element={<HostelManagement />} />
        <Route path='/Quan_ly_hoa_don' element={<InvoiceManagement />} />
        <Route path='/Quan_ly_dich_vu' element={<ServiceManagement />} />
        <Route path='/Quan_ly_hop_dongs' element={<ContractManagement />} />
      </Routes>
    </Box>
  )
}

export default App -->
