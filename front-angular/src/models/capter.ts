export interface Capter {
    id?: number;
   humidite?: number;
   temperature?: number;
   inondation?: number;
   niveauEau?: number;
}

export type PartialUser = Partial<Capter>;