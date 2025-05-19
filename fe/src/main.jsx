import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import App from './App.jsx'
import { ConfirmProvider } from 'material-ui-confirm' //Cau hinh MUI dialog
import theme from './theme.js'
import { ThemeProvider } from '@mui/material/styles'

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <ThemeProvider theme={theme}>
      <ConfirmProvider>
        <App />
      </ConfirmProvider>
    </ThemeProvider>
  </StrictMode>
)
