export interface RoomPut {
    idUtilisateur?: number;
    nom?: string;
    description?: string;
}

export type PartialUser = Partial<RoomPut>;