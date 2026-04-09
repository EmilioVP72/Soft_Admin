import apiClient from "@/api/axios";

export default {
    getAllInputs() {
        return apiClient.get('/inputs');
    },
    createInput(data: any) {
        return apiClient.post('/inputs', data);
    },
    getAllOutputs() {
        return apiClient.get('/outputs');
    },
    createOutput(data: any) {
        return apiClient.post('/outputs', data);
    }
}