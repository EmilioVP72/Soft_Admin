import apiClient from "@/api/axios";

export default {
    getAllSuppliers() {
        return apiClient.get<any>('/suppliers');
    },

    createSupplier(supplier: any) {
        return apiClient.post<any>('/suppliers', supplier);
    },

    updateSupplier(id: number, supplier: any) {
        return apiClient.put<any>(`/suppliers/${id}`, supplier);
    },

    deleteSupplier(id: number) {
        return apiClient.delete<any>(`/suppliers/${id}`);
    },

    //pago a proveedores

    getAllPaymentsToSuppliers(id: number) {
        return apiClient.get<any>(`/suppliers/${id}/payments`);
    },

    storePayment(id: number, paymentData: any) {
        return apiClient.post<any>(`/suppliers/${id}/payments`, paymentData);
    }
}