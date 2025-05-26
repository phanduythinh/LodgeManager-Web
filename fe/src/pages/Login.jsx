import { useState } from 'react'
import {
  Box,
  Button,
  TextField,
  Typography,
  Paper,
  Stack,
  Link
} from '@mui/material'
import { useForm } from 'react-hook-form'

function Login() {
  const [isLogin, setIsLogin] = useState(true);

  const {
    register,
    handleSubmit,
    watch,
    reset,
    formState: { errors }
  } = useForm();

  const onSubmit = (data) => {
    if (isLogin) {
      console.log('Đăng nhập:', data);
    } else {
      console.log('Đăng ký:', data);
    }
    reset();
  };

  const switchMode = () => {
    setIsLogin((prev) => !prev);
    reset();
  };

  return (
    <Box
      display="flex"
      justifyContent="center"
      alignItems="center"
      minHeight="520px"
    >
      <Paper elevation={4} sx={{ p: 4, width: 350 }}>
        <Typography variant="h5" align="center" gutterBottom>
          {isLogin ? 'Đăng nhập' : 'Đăng ký'}
        </Typography>

        <form onSubmit={handleSubmit(onSubmit)} noValidate>
          {!isLogin && (
            <TextField
              label="Email"
              type="email"
              fullWidth
              margin="normal"
              {...register('email', {
                required: 'Vui lòng nhập email',
                pattern: {
                  value: /^\S+@\S+$/i,
                  message: 'Email không hợp lệ'
                }
              })}
              error={Boolean(errors.email)}
              helperText={errors.email?.message}
            />
          )}

          <TextField
            label="Tên đăng nhập"
            fullWidth
            margin="normal"
            {...register('username', {
              required: 'Vui lòng nhập tên đăng nhập',
              minLength: {
                value: 4,
                message: 'Phải có ít nhất 4 ký tự'
              }
            })}
            error={Boolean(errors.username)}
            helperText={errors.username?.message}
          />

          <TextField
            label="Mật khẩu"
            type="password"
            fullWidth
            margin="normal"
            {...register('password', {
              required: 'Vui lòng nhập mật khẩu',
              minLength: {
                value: 6,
                message: 'Mật khẩu phải có ít nhất 6 ký tự'
              }
            })}
            error={Boolean(errors.password)}
            helperText={errors.password?.message}
          />

          {!isLogin && (
            <TextField
              label="Xác nhận mật khẩu"
              type="password"
              fullWidth
              margin="normal"
              {...register('confirmPassword', {
                required: 'Vui lòng xác nhận mật khẩu',
                validate: (value) =>
                  value === watch('password') || 'Mật khẩu không khớp'
              })}
              error={Boolean(errors.confirmPassword)}
              helperText={errors.confirmPassword?.message}
            />
          )}

          <Button
            type="submit"
            variant="contained"
            fullWidth
            sx={{ mt: 2 }}
          >
            {isLogin ? 'Đăng nhập' : 'Đăng ký'}
          </Button>
        </form>

        <Stack direction="row" justifyContent="center" sx={{ mt: 2 }}>
          <Typography variant="body2">
            {isLogin ? 'Chưa có tài khoản?' : 'Đã có tài khoản?'}{' '}
            <Link
              component="button"
              variant="body2"
              onClick={switchMode}
            >
              {isLogin ? 'Đăng ký' : 'Đăng nhập'}
            </Link>
          </Typography>
        </Stack>
      </Paper>
    </Box>
  )
}

export default Login
