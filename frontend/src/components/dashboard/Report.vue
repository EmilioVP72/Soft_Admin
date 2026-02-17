<script setup lang="ts">
import { 
  PresentationChartLineIcon,
  BuildingStorefrontIcon,
  UserCircleIcon
} from '@heroicons/vue/24/outline';
</script>

<template>
    <div class="reports-dashboard">
        <section class="reports-container">
            <h2>Reportes Rapidos</h2>
            <section class="reports">
                <section class="report">
                    <PresentationChartLineIcon class="icon"/>
                    <h3>Reporte de Ventas General</h3>
                    <p>Genera un reporte en PDF o Excel de las ventas por cada departamento del negocio</p>
                    <div class="report-actions">
                        <button class="btn-report">
                            Generar PDF
                        </button>
                        <button class="btn-excel">
                            Generar Excel
                        </button>
                    </div>
                </section>        
                <section class="report">
                    <BuildingStorefrontIcon class="icon"/>
                    <h3>Reporte de Ventas por Tienda</h3>
                    <p>Genera un reporte en PDF o Excel de las ventas por cada tienda del negocio</p>
                    <div class="report-actions">
                        <button class="btn-report">
                            Generar PDF
                        </button>
                        <button class="btn-excel">
                            Generar Excel
                        </button>
                    </div>
                </section>   
                <section class="report">
                    <UserCircleIcon class="icon"/>
                    <h3>Reporte de Empleados</h3>
                    <p>Genera un reporte en PDF o Excel datos relacionados con los empleados</p>
                    <div class="report-actions">
                        <button class="btn-report">
                            Generar PDF
                        </button>
                        <button class="btn-excel">
                            Generar Excel
                        </button>
                    </div>
                </section>       
            </section>
        </section>
    </div>
</template>

<style scoped>
@import "@/assets/styles/dashboard/componentes/report.css";
</style>