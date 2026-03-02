<script setup lang="ts">
import { 
  DocumentCurrencyDollarIcon,
  BanknotesIcon, 
  TableCellsIcon
} from '@heroicons/vue/24/outline';

</script>

<template>
    <div class="fast-action-dashboard">
        <section class="actions-container">
            <h2>Acciones Rápidas</h2>
            <section class="actions" >
                <!--<section class="action" >
                    <DocumentCurrencyDollarIcon class="icon"/>
                    <h3>Realizar Factura</h3>
                    <p>Para crear una factura ingresando los datos necesarios</p>
                    <RouterLink to="" class="btn-action" >
                        Crear Factura
                    </RouterLink>
                </section>  IGNORE -->     
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