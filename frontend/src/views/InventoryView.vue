<template>
  <div class="page">
    <div class="header-actions">
      <div>
        <h1>Daftar Inventaris</h1>
        <p class="subtitle">
          Kelola data stok dan barang inventaris
        </p>
      </div>
      <button
        class="btn btn-primary"
        @click="openCreateModal"
      >
        + Tambah Barang
      </button>
    </div>

    <!-- Alert Messages -->
    <div
      v-if="successMessage"
      class="alert alert-success"
    >
      {{ successMessage }}
    </div>
    <div
      v-if="errorMessage"
      class="alert alert-error"
    >
      {{ errorMessage }}
    </div>

    <!-- Loading State -->
    <p
      v-if="loading"
      class="info-text"
    >
      Memuat data inventaris...
    </p>

    <!-- Empty State -->
    <p
      v-else-if="items.length === 0"
      class="info-text"
    >
      Belum ada barang di inventaris. Silakan tambah barang baru.
    </p>

    <!-- Inventory Table -->
    <table v-else>
      <thead>
        <tr>
          <th>Nama Barang</th>
          <th>Kategori</th>
          <th>Stok</th>
          <th>Harga</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="item in items"
          :key="item.id"
        >
          <td><strong>{{ item.name }}</strong></td>
          <td>{{ item.category }}</td>
          <td>{{ item.stock }} unit</td>
          <td>{{ formatPrice(item.price) }}</td>
          <td class="action-buttons">
            <button
              class="btn btn-sm btn-edit"
              @click="openEditModal(item)"
            >
              Edit
            </button>
            <button
              class="btn btn-sm btn-danger"
              @click="deleteItem(item.id)"
            >
              Hapus
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal Form (Tambah / Edit) -->
    <div
      v-if="showModal"
      class="modal-backdrop"
      @click.self="closeModal"
    >
      <div class="modal">
        <h3>{{ isEditing ? 'Edit Barang Inventaris' : 'Tambah Barang Baru' }}</h3>

        <form @submit.prevent="saveItem">
          <div class="form-group">
            <label for="name">Nama Barang</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              placeholder="Contoh: Monitor LG 27 Inch"
            >
          </div>

          <div class="form-group">
            <label for="category">Kategori</label>
            <input
              id="category"
              v-model="form.category"
              type="text"
              required
              placeholder="Contoh: Elektronik"
            >
          </div>

          <div class="form-row">
            <div class="form-group col">
              <label for="stock">Stok</label>
              <input
                id="stock"
                v-model.number="form.stock"
                type="number"
                min="0"
                required
              >
            </div>
            <div class="form-group col">
              <label for="price">Harga (Rp)</label>
              <input
                id="price"
                v-model.number="form.price"
                type="number"
                min="0"
                required
              >
            </div>
          </div>

          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              @click="closeModal"
            >
              Batal
            </button>
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="saving"
            >
              {{ saving ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import api from '../utils/api'
import { formatPrice } from '../utils/formatPrice'

const items = ref([])
const loading = ref(true)
const saving = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const showModal = ref(false)
const isEditing = ref(false)
const editingId = ref(null)

const form = reactive({
  name: '',
  category: '',
  stock: 0,
  price: 0,
})

function showSuccess(msg) {
  successMessage.value = msg
  setTimeout(() => {
    successMessage.value = ''
  }, 4000)
}

async function fetchInventory() {
  loading.value = true
  errorMessage.value = ''

  try {
    const response = await api.get('/inventory')
    items.value = response.data.data || response.data
  } catch (err) {
    errorMessage.value = err.response?.data?.message || err.message || 'Gagal memuat inventaris.'
  } finally {
    loading.value = false
  }
}

function openCreateModal() {
  isEditing.value = false
  editingId.value = null
  form.name = ''
  form.category = ''
  form.stock = 0
  form.price = 0
  showModal.value = true
}

function openEditModal(item) {
  isEditing.value = true
  editingId.value = item.id
  form.name = item.name
  form.category = item.category
  form.stock = item.stock
  form.price = item.price
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

async function saveItem() {
  saving.value = true
  errorMessage.value = ''

  try {
    if (isEditing.value) {
      await api.put(`/inventory/${editingId.value}`, form)
      showSuccess('Item inventaris berhasil diperbarui.')
    } else {
      await api.post('/inventory', form)
      showSuccess('Item inventaris baru berhasil ditambahkan.')
    }
    closeModal()
    await fetchInventory()
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Gagal menyimpan item inventaris.'
  } finally {
    saving.value = false
  }
}

async function deleteItem(id) {
  if (!confirm('Apakah Anda yakin ingin menghapus item ini?')) {
    return
  }

  try {
    await api.delete(`/inventory/${id}`)
    showSuccess('Item inventaris berhasil dihapus.')
    await fetchInventory()
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Gagal menghapus item inventaris.'
  }
}

onMounted(fetchInventory)
</script>

<style scoped>
.header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.subtitle {
  color: #666;
  margin-top: -10px;
}

.info-text {
  padding: 20px 0;
  color: #666;
}

.action-buttons {
  display: flex;
  gap: 8px;
}

.alert {
  padding: 12px 16px;
  border-radius: 4px;
  margin-bottom: 16px;
}

.alert-success {
  background: #dcfce7;
  color: #166534;
}

.alert-error {
  background: #fee2e2;
  color: #991b1b;
}

.btn {
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}

.btn-primary {
  background: #2563eb;
  color: white;
}

.btn-edit {
  background: #f59e0b;
  color: white;
}

.btn-danger {
  background: #ef4444;
  color: white;
}

.btn-secondary {
  background: #9ca3af;
  color: white;
}

.btn-sm {
  padding: 4px 10px;
  font-size: 0.85rem;
}

/* Modal styles */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: white;
  padding: 24px;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-weight: bold;
  margin-bottom: 6px;
}

.form-group input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.form-row {
  display: flex;
  gap: 16px;
}

.col {
  flex: 1;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}
</style>
