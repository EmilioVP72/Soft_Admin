import apiClient from "../api/payment";


export default {
    getPayments(){
        return apiClient.get<any>('/payments');
    }
}