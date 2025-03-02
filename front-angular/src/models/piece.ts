export interface Piece {
    id?: number;
   nom?: string;
   description?: string;
}

export type PartialUser = Partial<Piece>;