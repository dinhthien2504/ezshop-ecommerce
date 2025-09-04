import axios from 'axios'
import { ref } from 'vue'

const url = import.meta.env.VITE_API_URL
const api_url = url ?? 'http://localhost:8000/api'

const api = axios.create({
  baseURL: api_url
})

api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
}, error => Promise.reject(error))

export default api
