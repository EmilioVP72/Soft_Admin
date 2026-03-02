// Seccion: "Importaciones"
// Explicacion: Se importa el cliente HTTP configurado y los tipos de datos que usa el servicio
import apiClient from "@/api/employee";
import type { Employee, PostEmployee } from "@/interfaces/EmployeeInterfaces";

// Seccion: "CRUD de Empleados"
// Explicacion: Expone las peticiones HTTP para listar, obtener, crear, actualizar y eliminar empleados;
//              cada metodo retorna la promesa de axios que el componente resuelve con await
export default {
    getEmployees() {
        return apiClient.get('/employees/all');
    },

    getEmployee(id: number) {
        return apiClient.get(`/employees/${id}`);
    },

    createEmployee(employee: PostEmployee) {
        return apiClient.post('/employees/create', employee);
    },

    updateEmployee(id: number, employee: Partial<PostEmployee>) {
        return apiClient.put(`/employees/update/${id}`, employee);
    },

    deleteEmployee(id: number) {
        return apiClient.delete(`/employees/force-delete/${id}`);
    }   
}