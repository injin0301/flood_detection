import { Piece } from "./piece";

export interface User {
    id?: number;
    email?: string;
    password?: string;
    nom?: string;
    prenom?: string;
    tel?: string;
    city?: string;
    zipCode?: string;
    piece?: Piece[]
}

export type PartialUser = Partial<User>;