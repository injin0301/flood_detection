export interface UserPut {
    id?: number;
    email?: string;
    password?: string;
    nom?: string;
    prenom?: string;
    tel?: string;
    city?: string;
    zipCode?: number;
    piece?: object
    roles: string[]
}

export type PartialUser = Partial<UserPut>;