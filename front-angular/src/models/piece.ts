import { Capteur } from "./capteur";
import { User } from "./user";

export interface Piece {
   id?: number;
   nom?: string;
   description?: string;
   utilisateur?: User;
   capteur?: Capteur[];
}

export type PartialUser = Partial<Piece>;