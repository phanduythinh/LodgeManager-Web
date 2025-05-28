import { useState } from 'react'
import {
  Box,
  Button,
  TextField,
  Typography,
  Paper,
  Stack,
  Link,
  Alert,
  CircularProgress
} from '@mui/material'
import { useForm } from 'react-hook-form'
import { authService } from '../apis/services'

function Login() {
  const [isLogin, setIsLogin] = useState(true);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  const {
    register,
    handleSubmit,
    watch,
    reset,
    formState: { errors }
  } = useForm();

  const onSubmit = async (data) => {
    setLoading(true);
    setError('');
    setSuccess('');
    
    try {
      if (isLogin) {
        // Đăng nhập
        const response = await authService.login({
          email: data.username, // Hoặc username tùy vào API backend
          password: data.password
        });
        
        // Lưu token và thông tin người dùng vào localStorage
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        
        setSuccess('Đăng nhập thành công!');
        
        // Chuyển hướng sau khi đăng nhập thành công
        setTimeout(() => {
          window.location.href = '/';
        }, 1500);
      } else {
        // Đăng ký
        const response = await authService.register({
          name: data.username,
          email: data.email,
          password: data.password,
          password_confirmation: data.confirmPassword
        });
        
        setSuccess('Đăng ký thành công! Vui lòng đăng nhập.');
        setTimeout(() => {
          setIsLogin(true);
          reset();
        }, 1500);
      }
    } catch (err) {
      console.error('Lỗi:', err);
      setError(
        err.response?.data?.message || 
        'Có lỗi xảy ra. Vui lòng thử lại sau.'
      );
    } finally {
      setLoading(false);
    }
  };

  const switchMode = () => {
    setIsLogin((prev) => !prev);
    setError('');
    setSuccess('');
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

        {error && (
          <Alert severity="error" sx={{ mb: 2 }}>
            {error}
          </Alert>
        )}

        {success && (
          <Alert severity="success" sx={{ mb: 2 }}>
            {success}
          </Alert>
        )}

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
            disabled={loading}
            sx={{ mt: 2 }}
          >
            {loading ? (
              <>
                <CircularProgress size={24} color="inherit" sx={{ mr: 1 }} />
                Đang xử lý...
              </>
            ) : (
              isLogin ? 'Đăng nhập' : 'Đăng ký'
            )}
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
