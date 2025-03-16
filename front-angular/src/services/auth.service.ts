import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { HttpHeaders } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://localhost:8080/api'; // URL du backend Symfony

  constructor(private http: HttpClient) {}

  login(credentials: { email: string; password: string }): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, credentials);
  }

  register(userData: { email: string; password: string }): Observable<any> {
    console.log('Envoi des données avec header JSON:', userData);
    return this.http.post(`${this.apiUrl}/enregistrer/utilisateur`, userData, {
        headers: new HttpHeaders({ 'Content-Type': 'application/json' }) // Forcer JSON
    });
}
}
