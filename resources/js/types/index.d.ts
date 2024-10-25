export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
}

export interface Database{
    id: number;
    name: string;
    host: string;
    port: number;
    username: string;
    password: string;
    database: string;
    connected_at: string;
    created_at: string;
    updated_at: string;
}

export interface SavedReport{
    id: number;
    database_id: number;
    prompt: string;
    query: string;
    created_at: string;
    updated_at: string;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
};
