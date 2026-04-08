import apiClient from "@/api/axios";

export default {
    getAllInputs() {
        return apiClient.get('/inputs');
    },
    getAllOutputs() {
        return apiClient.get('/outputs');
    }
}