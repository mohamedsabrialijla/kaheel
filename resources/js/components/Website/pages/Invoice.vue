<template>
  <div class="card">
    <div class="card-header">
      <h3>فاتورة رقم {{ invoice.id }}</h3>
      <a :href="`/api/invoices/${invoice.id}/pdf`" target="_blank" class="btn btn-primary">تحميل PDF</a>
    </div>
    <div class="card-body">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>الوصف</th>
            <th>الكمية</th>
            <th>سعر الوحدة</th>
            <th>الضريبة</th>
            <th>الإجمالي</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in invoice.items" :key="item.id">
            <td>{{ item.description }}</td>
            <td>{{ item.qty }}</td>
            <td>{{ item.price }}</td>
            <td>{{ item.tax }}%</td>
            <td>{{ item.total }}</td>
          </tr>
        </tbody>
      </table>
      <div class="mt-4 text-end">
        <strong>الإجمالي: {{ invoice.total }} ر.س</strong>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const invoice = ref({ items: [] })

onMounted(async () => {
  const { data } = await axios.get(`/api/invoices/${window.location.pathname.split('/').pop()}`)
  invoice.value = data
})
</script>
