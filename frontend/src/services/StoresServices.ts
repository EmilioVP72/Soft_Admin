import apiClient from "../api/stores";
import type { Store } from "../interfaces/StoresInterfaces";

export default{
    getSalesByDepartment(){
        return apiClient.get<Store>('/sales/byDepartment');
    },

    getSalesByDepartmentByStore(storeId: number){
        return apiClient.get<Store>(`/sales/byDepartment/${storeId}`);
    }
}