<template>
  <AdminLayout>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-tachometer-alt"></i> Panel principal
            </h3>
          </div>
          <div class="card-body">
            <p>Bienvenido, <strong>{{ user?.name || 'Usuario' }}</strong> 👋</p>
            <p>Este es tu panel principal de StyloSoft.</p>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '../Layouts/AdminLayout.vue'
import { usePage } from '@inertiajs/vue3'
const { props } = usePage()
const user = props.auth?.user || {}
</script>
