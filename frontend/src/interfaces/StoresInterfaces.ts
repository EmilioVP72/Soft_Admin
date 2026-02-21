export interface Store {
    success: boolean;
    message: string;
    data: {
        id_department: number;
        department: String;
        general_department: String;
        total_sales: number;
        total_transactions: number;
        total_quantity: number;
    }
}

export interface StoreList {
    success: boolean;
    message: string;
    data: {
        id_store: number;
        name: String;
    }[]
}