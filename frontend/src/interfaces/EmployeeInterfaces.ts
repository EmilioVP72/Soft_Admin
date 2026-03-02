// Seccion: "Interface de respuesta GET"
// Explicacion: Define la estructura completa que devuelve el backend al consultar un empleado;
//              incluye datos del empleado, del usuario asociado y de la sucursal
export interface Employee {
    flag: boolean;
    code: number;
    message: string;
    data: {
        id_employee: number;
        full_name: string;
        email: string;
        phone: string;
        document_type: string;
        document_number: string;
        position: string;
        salary: number;
        status: string;
        status_label: string;
        hire_date: string;
        end_date: string | null;
        notes: string | null;
        created_at: string;
        updated_at: string;
    },
    user: {
        id_user: number;
        name: string;
        email: string;
    }
    store: {
        id_store: number;
        name: string;
        colony: string;
        street: string;
    }

}

// Seccion: "Interface de creacion y actualizacion"
// Explicacion: Define los campos que el frontend envia al backend para crear o actualizar
//              un empleado; fk_id_store es la clave foranea que relaciona al empleado con su sucursal
export interface PostEmployee {
    full_name: string;
    email: string;
    phone: string;
    document_type: string;
    document_number: string;
    position: string;
    salary: number;
    status: string;
    hire_date: string;
    end_date?: string | null;
    notes?: string | null;
    fk_id_store: number;
}