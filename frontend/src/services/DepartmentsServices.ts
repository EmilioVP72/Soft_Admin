import apiClient from "@/api/axios";
export default {
    getAllDepartments(){
        return apiClient.get('/departments/1');
    }
}