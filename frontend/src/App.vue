<template>
  <header>
    <nav>
      <div class="nav-links">
        <RouterLink to="/">
          Home
        </RouterLink>
        <RouterLink to="/inventory">
          Inventory
        </RouterLink>
      </div>
      <div class="nav-auth">
        <button
          v-if="isAuthenticated"
          class="nav-btn-logout"
          @click="logout"
        >
          Logout
        </button>
        <RouterLink
          v-else
          to="/login"
        >
          Login
        </RouterLink>
      </div>
    </nav>
  </header>

  <main>
    <RouterView />
  </main>
</template>


<script setup>
import { useRouter } from 'vue-router'
import api from './utils/api'
import { isAuthenticated, clearAuth } from './utils/auth'

const router = useRouter()

async function logout() {
  try {
    await api.post('/logout')
  } catch {
    // Token mungkin sudah tidak berlaku.
  } finally {
    clearAuth()
    router.push('/login')
  }
}
</script>


<style scoped>
nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.nav-links {
  display: flex;
  gap: 20px;
}

.nav-btn-logout {
  background: transparent;
  border: 1px solid #ef4444;
  color: #ef4444;
  padding: 6px 14px;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}

.nav-btn-logout:hover {
  background: #ef4444;
  color: white;
}
</style>
