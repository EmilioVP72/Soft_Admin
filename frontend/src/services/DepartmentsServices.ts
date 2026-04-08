import apiClient from "@/api/axios";

export default {
    // ---- DEPARTAMENTOS GENERALES ----
    getAllGeneralDepartments() {
        return apiClient.get('/general-departments/all');
    },
    getGeneralDepartmentsByStore(storeId: number) {
        return apiClient.get(`/general-departments/store/${storeId}`);
    },
    getGeneralDepartment(id: number) {
        return apiClient.get(`/general-departments/${id}`);
    },
    createGeneralDepartment(data: any) {
        return apiClient.post('/general-departments/create', data);
    },
    updateGeneralDepartment(id: number, data: any) {
        return apiClient.put(`/general-departments/update/${id}`, data);
    },
    deleteGeneralDepartment(id: number) {
        return apiClient.delete(`/general-departments/delete/${id}`);
    },

    // ---- DEPARTAMENTOS ESPECÍFICOS ----
    getAllDepartments() {
        return apiClient.get('/departments/all');
    },
    getDepartmentsByStore(storeId: number) {
        return apiClient.get(`/departments/store/${storeId}`);
    },
    getDepartment(id: number) {
        return apiClient.get(`/departments/${id}`);
    },
    createDepartment(data: any) {
        return apiClient.post('/departments/create', data);
    },
    updateDepartment(id: number, data: any) {
        return apiClient.put(`/departments/update/${id}`, data);
    },
    deleteDepartment(id: number) {
        return apiClient.delete(`/departments/delete/${id}`);
    }
};