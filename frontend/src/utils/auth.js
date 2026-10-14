
import { ref } from 'vue'

export const isAuthenticated = ref(!!localStorage.getItem('token'))

export function setAuth(token, user) {
  localStorage.setItem('token', token)
  localStorage.setItem('user', JSON.stringify(user))
  isAuthenticated.value = true
}

export function clearAuth() {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  isAuthenticated.value = false
}
