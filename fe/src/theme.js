import { createTheme } from '@mui/material/styles';

const theme = createTheme({
  cssVariables: {
    colorSchemeSelector: 'data-toolpad-color-scheme',
  },
  colorSchemes: { light: true, dark: true },
  breakpoints: {
    values: {
      xs: 0,
      sm: 600,
      md: 600,
      lg: 1200,
      xl: 1536,
    },
  },
  components: {
    // MuiCssBaseline: {
    //   styleOverrides: {
    //     ':root': {
    //       // Không chắc có tác dụng với Toolpad, nhưng vẫn để
    //       '--Toolpad-navigation-drawer-width': '250px',
    //     },
    //     // Đây là phần quan trọng: ép các class mặc định của Toolpad drawer
    //     '.ToolpadNavigationDrawer-paper': {
    //       width: '250px !important',
    //     },
    //     '[class*="MuiDrawer-paper"]': {
    //       width: '250px !important',
    //     },
    //     '.MuiDrawer-docked': {
    //       width: '250px !important',
    //     }
    //   },
    // },
  },
});

export default theme;
