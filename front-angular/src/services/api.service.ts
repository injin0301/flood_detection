import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { Piece } from '../models/piece';
import { map, tap } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class ApiService {

  private apiUrl = 'http://localhost:80/api'; // Remplace par l'URL de ton backend

  private tokenMock = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3NDExNzQxMjAsImV4cCI6MTc0MTE3NzcyMCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInBhc3NwaHJhc2UiOiIxMiIsInVzZXJuYW1lIjoia2lraUBnbWFpbC5jb20ifQ.RI8nb66RCVIsTHkhnZh4gFk3TFKSY2YCy-TEkqkW76ciYcDwtcMZICTJ5X1fVr4wo8ppXxvEU0sHiZ1x5rYAJyB_O7J1lJS6ma6Zzp2duChRfSFviqTbwhYMjRLL9C_gWlsxdQbJz5EvSiVOmdlJqYpgqwhRJ9yuLWiQkgNXBlDZFGGoit-yF07TK9APuwtvZ2XmBdS66tFOM0RkNdrAO3yoF2C2628vfYo3nG7xyyQq8HeTo0XjOwjXdEfYS2kZwjpmaVPHcuzrs3jT_NuZtoJeXEZlM-L2UmzACkoxwWazkeLUBnkSZL3-7j19ouYpEp1IKFRD4AHfP4SpZLfMiw'  
  
  constructor(private http: HttpClient) {}

  getPieces(): Observable<any[]> {
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${this.tokenMock}`, // Ajout du header d'authentification
    });

    return this.http.get<any>(this.apiUrl + '/toutes/pieces', { headers }).pipe(
      tap(response => console.log('Réponse API :', response)), // Log pour débogage
      map(response => Array.isArray(response) ? response : response.piece || []) // Gère le cas où l'API retourne un objet ou directement un tableau
    );
  }

  getPiece(roomId: any): Observable<any> {
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${this.tokenMock}`, // Ajout du header d'authentification
    });
    return this.http.get(this.apiUrl + '/piece/' + roomId, { headers });
  }

  getUsers(): Observable<any[]> {
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${this.tokenMock}`, // Ajout du header d'authentification
    });

    return this.http.get<any>(this.apiUrl + '/tous/utilisateurs', { headers }).pipe(
      tap(response => console.log('Réponse API :', response)), // Log pour débogage
      map(response => Array.isArray(response) ? response : response.utilisateur || []) // Gère le cas où l'API retourne un objet ou directement un tableau
    );
  }

  updateUser(userId: any, userData: any): Observable<any> {
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${this.tokenMock}`, // Ajout du header d'authentification
    });
    return this.http.put(this.apiUrl + '/utilisateur/' + userId, userData, { headers });
  }

  updatePiece(roomId: any, roomData: any): Observable<any> {
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${this.tokenMock}`, // Ajout du header d'authentification
    });
    return this.http.put(this.apiUrl + '/piece/' + roomId + '/put', roomData, { headers });
  }
}
