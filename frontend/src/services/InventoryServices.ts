import apiClient from '@/api/axios';

export default {
    
    getAllProducts(){
        return apiClient.get('/products');
    },
    
    createProduct(product: object){
        return apiClient.post('/products', product);
    },
    
    updateProduct(id: number, product: object){
        return apiClient.put(`/products/${id}`, product);
    },

    //registro de inventario y tickets

    getAllRegistersOfProduct(id: number){
        return apiClient.get(`/products/${id}/inventories`);
    },

    createNewTicket(ticketData: object){
        return apiClient.post(`/product-inventories`, ticketData);
    },

    verifyInventory(id: number, data: object){
        return apiClient.post(`/product-inventories/${id}/verify`, data);
    }

}