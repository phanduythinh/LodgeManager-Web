import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import App from './App.jsx'
import { ConfirmProvider } from "material-ui-confirm" //Cau hinh MUI dialog

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <ConfirmProvider>
      <App />
    </ConfirmProvider>
  </StrictMode>,
)
