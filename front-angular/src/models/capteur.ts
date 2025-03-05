export interface Capteur {
   id?: number;
   humidite?: number;
   temperature?: number;
   inondation?: boolean;
   niveauEau?: number;
}

export type PartialUser = Partial<Capteur>;