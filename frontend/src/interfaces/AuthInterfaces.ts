export interface LoginResponse {
    flag: boolean;
    code: number;
    message: string;
    data: {
        user: {
            id_user: number;
            email: string;
            phone: string;
            email_verified_at: string | null;
            created_at: string;
            updated_at: string;
        },
        token: string;
        token_type: "Bearer";
        expires_in: number;
    }
}