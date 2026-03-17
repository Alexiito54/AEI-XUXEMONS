import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient) { }

  register(userData: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, userData);
  }

  login(credentials: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, credentials);
  }

  getUser(): Observable<any> {
    return this.http.get(`${this.apiUrl}/user`);
  }

  updateUser(userData: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/user`, userData);
  }

  changePassword(passwordData: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/change-password`, passwordData);
  }

  deleteUser(): Observable<any> {
    return this.http.delete(`${this.apiUrl}/user`);
  }

  logout(): Observable<any> {
    return this.http.post(`${this.apiUrl}/logout`, {});
  }
}
