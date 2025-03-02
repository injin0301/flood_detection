export interface User {
    id?: number;
    email?: string;
    password?: string;
    nom?: string;
    prenom?: string;
    tel?: number;
}

export type PartialUser = Partial<User>;