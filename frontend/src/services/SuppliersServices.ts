import apiClient from "@/api/suppliers";

export default {
    getAllSuppliers(){
        return apiClient.get<any>('/suppliers');
    },
}