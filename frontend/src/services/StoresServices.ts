// Seccion: "Importaciones"
// Explicacion: Se importa el cliente HTTP de sucursales y el tipo de respuesta Store
import apiClient from "../api/stores";
import type { Store } from "../interfaces/StoresInterfaces";
// eslint-disable-next-line @typescript-eslint/no-explicit-any

// Seccion: "Consultas de ventas y sucursales"
// Explicacion: Expone los endpoints de ventas por departamento, ventas por sucursal
//              y listado de sucursales; aun no incluye CRUD completo de sucursales
export default {
    getSalesByDepartment() {
        return apiClient.get<any>('/sales/byDepartment');
    },

    getSalesByDepartmentByStore(storeId: number) {
        return apiClient.get<any>(`/sales/byStore/${storeId}`);
    },

    getStores() {
        return apiClient.get<any>('/stores/all');
    },

    createStore(storeData: Partial<Store>) {
        return apiClient.post('/stores/StoreStore', storeData);
    },

    getOneStore(storeId: number) {
        return apiClient.get(`/stores/OneStore/${storeId}`);
    },

    updateStore(storeId: number, storeData: Partial<Store>) {
        return apiClient.put(`/stores/UpdateStore/${storeId}`, storeData);
    },

    deleteStore(storeId: number) {
        return apiClient.delete(`/stores/DeleteStore/${storeId}`);
    },

    //update api sales
    createSale(saleData: any) {
        return apiClient.post('/sales', saleData);
    },

    getSalesbyDepartmentForStore() {
        return apiClient.get<any>('/sales');
    },

    getDepartments() {
        return apiClient.get<any>('/departments');
    },

    getDepartmentsByStore(storeId: number) {
        return apiClient.get<any>(`/departments/${storeId}`);
    },
}