import * as React from 'react';
import PhiDichVu from './pages/DanhMucDuLieu/PhiDichVu';
import Phong from './pages/DanhMucDuLieu/Phong';
import ToaNha from './pages/DanhMucDuLieu/ToaNha';
import HopDong from './pages/KhachHang/HopDong';
import KhachHang from './pages/KhachHang/KhachHang';

import PropTypes from 'prop-types';
import Box from '@mui/material/Box';
import Typography from '@mui/material/Typography';
import DashboardIcon from '@mui/icons-material/Dashboard';
import ShoppingCartIcon from '@mui/icons-material/ShoppingCart';
import BarChartIcon from '@mui/icons-material/BarChart';
import DescriptionIcon from '@mui/icons-material/Description';
import LayersIcon from '@mui/icons-material/Layers';
import { AppProvider } from '@toolpad/core/AppProvider';
import { DashboardLayout } from '@toolpad/core/DashboardLayout';
import { DemoProvider, useDemoRouter } from '@toolpad/core/internal';
import theme from './theme';

const NAVIGATION = [
  {
    kind: 'header',
    title: 'QUẢN LÝ & VẬN HÀNH',
  },
  {
    segment: 'DanhMucDuLieu',
    title: 'Danh mục dữ liệu',
    icon: <DashboardIcon />,
    children: [
      {
        segment: 'ToaNha',
        title: 'Tòa nhà',
        icon: <DescriptionIcon />,
      },
      {
        segment: 'Phong',
        title: 'Phòng',
        icon: <DescriptionIcon />,
      },
      {
        segment: 'phiDichVu',
        title: 'Phí dịch vụ',
        icon: <DescriptionIcon />,
      }
    ],
  },
  {
    segment: 'KhachHang',
    title: 'Khách hàng',
    icon: <ShoppingCartIcon />, children: [
      {
        segment: 'HopDong',
        title: 'Hợp đồng',
        icon: <DescriptionIcon />,
      },
      {
        segment: 'KhachHang',
        title: 'Khách hàng',
        icon: <DescriptionIcon />,
      }
    ],
  },
  {
    kind: 'divider',
  },
  {
    kind: 'header',
    title: 'Báo cáo',
  },
  {
    segment: 'BaoCaoTaiChinh',
    title: 'Báo cáo tài chính',
    icon: <BarChartIcon />,
    children: [
      {
        segment: 'sales',
        title: 'Sales',
        icon: <DescriptionIcon />,
      },
      {
        segment: 'traffic',
        title: 'Traffic',
        icon: <DescriptionIcon />,
      },
    ],
  }
];

function DemoPageContent({ pathname }) {
  let content;

  switch (pathname) {
    case '/DanhMucDuLieu/ToaNha':
      content = <ToaNha />;
      break;
    case '/DanhMucDuLieu/Phong':
      content = <Phong />;
      break;
    case '/DanhMucDuLieu/phiDichVu':
      content = <PhiDichVu />;
      break;
    case '/KhachHang/HopDong':
      content = <HopDong />;
      break;
    case '/KhachHang/KhachHang':
      content = <KhachHang />;
      break;
    // case '/reports/traffic':
    //   content = <ViewStatistics />;
    //   break;
    default:
      content = (
        <Box
          sx={{
            py: 4,
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'center',
            textAlign: 'center',
          }}
        >
          <Typography>Xin chào Đại Ca</Typography>
        </Box>
      );
  }

  return content;
}

DemoPageContent.propTypes = {
  pathname: PropTypes.string.isRequired,
};

function App(props) {
  const { window } = props;

  const router = useDemoRouter('/');

  // Remove this const when copying and pasting into your project.
  const demoWindow = window !== undefined ? window() : undefined;

  return (
    // Remove this provider when copying and pasting into your project.
    <DemoProvider window={demoWindow}>
      {/* preview-start */}
      <AppProvider
        navigation={NAVIGATION}
        router={router}
        theme={theme}
        window={demoWindow}
        title="Quản lý tòa nhà"
      >
        <DashboardLayout>
          <Box>
            <DemoPageContent pathname={router.pathname} />
          </Box>
        </DashboardLayout>
      </AppProvider>
      {/* preview-end */}
    </DemoProvider>
  );
}

App.propTypes = {
  window: PropTypes.func,
};

export default App
