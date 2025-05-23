import axios from 'axios';

const api = axios.create({
    baseURL: 'http://lodge-manager.test/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    withCredentials: true
});

export default api; 