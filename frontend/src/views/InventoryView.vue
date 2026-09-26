<template>
  <div class="page">
    <h1>Inventory</h1>

    <p v-if="loading">
      Memuat data...
    </p>

    <p v-else-if="error">
      Gagal mengambil data inventory: {{ error }}
    </p>

    <table v-else>
      <thead>
        <tr>
          <th>Nama</th>
          <th>Kategori</th>
          <th>Stok</th>
          <th>Harga</th>
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="item in items"
          :key="item.id"
        >
          <td>{{ item.name }}</td>
          <td>{{ item.category }}</td>
          <td>{{ item.stock }}</td>
          <td>{{ formatPrice(item.price) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { formatPrice } from '../utils/formatPrice'

const items = ref([])
const loading = ref(true)
const error = ref('')

const apiUrl = import.meta.env.VITE_API_URL

async function fetchInventory() {
  try {
    const response = await fetch(`${apiUrl}/inventory`)

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`)
    }

    items.value = await response.json()
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

onMounted(fetchInventory)
</script>
