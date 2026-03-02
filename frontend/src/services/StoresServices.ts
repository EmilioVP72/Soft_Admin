// Seccion: "Importaciones"
// Explicacion: Se importa el cliente HTTP de sucursales y el tipo de respuesta Store
import apiClient from "../api/stores";
import type { Store } from "../interfaces/StoresInterfaces";

// Seccion: "Consultas de ventas y sucursales"
// Explicacion: Expone los endpoints de ventas por departamento, ventas por sucursal
//              y listado de sucursales; aun no incluye CRUD completo de sucursales
export default{
    getSalesByDepartment(){
        return apiClient.get<Store>('/sales/byDepartment');
    },

    getSalesByDepartmentByStore(storeId: number){
        return apiClient.get<Store>(`/sales/byStore/${storeId}`);
    },

    getStores(){
        return apiClient.get<Store[]>('/stores/all');
    },

    createStore(storeData: Partial<Store>){
        return apiClient.post('/stores/StoreStore', storeData);
    },

    getOneStore(storeId: number){
        return apiClient.get(`/stores/OneStore/${storeId}`);
    },

    updateStore(storeId: number, storeData: Partial<Store>){
        return apiClient.put(`/stores/UpdateStore/${storeId}`, storeData);
    },

    deleteStore(storeId: number){
        return apiClient.delete(`/stores/DeleteStore/${storeId}`);
    }
}