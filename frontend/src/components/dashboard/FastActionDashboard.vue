<script setup lang="ts">
import { 
  BanknotesIcon, 
  TableCellsIcon,
  ClipboardDocumentListIcon
} from '@heroicons/vue/24/outline';

</script>

<template>
    <div class="fast-action-dashboard">
        <section class="actions-container">
            <h2>Acciones Rápidas</h2>
            <section class="actions" >
                <section class="action" >
                    <ClipboardDocumentListIcon class="icon"/>
                    <h3>Inventario</h3>
                    <p>Registrar mercancía entrante, verificar su estado y generar reportes para revisión con proveedores en caso de daños o inconsistencias.
                    </p>
                    <RouterLink to="/inventory" class="btn-action">
                        Ver Inventario
                    </RouterLink>
                </section>     
                <section class="action">
                    <BanknotesIcon class="icon"/>
                    <h3>Verificar Promociones</h3>
                    <p>Calcular el total de ventas de un proveedor para obtener promociones</p>
                    <RouterLink to="/calculate" class="btn-action">
                        Calcular Promociones
                    </RouterLink>
                </section>   
                <section class="action">
                    <TableCellsIcon class="icon"/>
                    <h3>Datos del Negocio</h3>
                    <p>Visualizar datos sobre sucursales, empleados y otros aspectos del negocio</p>
                    <RouterLink to="/data" class="btn-action">
                        Ver Datos
                    </RouterLink>
                </section>       
            </section>
        </section>
    </div>
</template>

<style scoped>
@import "@/assets/styles/dashboard/componentes/fastActionsDashboard.css";
</style>