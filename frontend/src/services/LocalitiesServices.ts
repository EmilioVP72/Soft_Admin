import apiClient from "@/api/localities";

export default {
    getLocalities(){
        return apiClient.get('/localities/all');
    },

    getOneLocality(localityId: number){
        return apiClient.get(`/localities/OneLocality/${localityId}`);
    },

    
}   