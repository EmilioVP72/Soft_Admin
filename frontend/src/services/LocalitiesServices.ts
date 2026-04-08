import apiClient from "@/api/axios";

export default {
    getLocalities(){
        return apiClient.get('/localities/all');
    },

    getOneLocality(localityId: number){
        return apiClient.get(`/localities/OneLocality/${localityId}`);
    },

    
}   