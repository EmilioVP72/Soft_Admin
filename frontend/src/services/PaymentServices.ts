import apiClient from "@/api/axios";


export default {
    getPayments(){
        return apiClient.get<any>('/payments');
    }
}