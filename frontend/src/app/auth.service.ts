import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient, private router: Router) {}

  register(userData: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, userData);
  }

  login(credentials: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, credentials).pipe(
      tap((res: any) => {
        localStorage.setItem('token', res.token);
        localStorage.setItem('user', JSON.stringify(res.user));
      })
    );
  }

  logout(): Observable<any> {
    return this.http.post(`${this.apiUrl}/logout`, {}).pipe(
      tap(() => {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        this.router.navigate(['/login']);
      })
    );
  }

  getToken(): string | null {
    return localStorage.getItem('token');
  }

  getStoredUser(): any {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  }

  isLoggedIn(): boolean {
    return !!this.getToken();
  }

  isAdmin(): boolean {
    return this.getStoredUser()?.role === 'admin';
  }

  getUser(): Observable<any> {
    return this.http.get(`${this.apiUrl}/user`);
  }

  updateUser(userData: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/user`, userData);
  }

  changePassword(passwordData: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/user/password`, passwordData);
  }

  deleteUser(): Observable<any> {
    return this.http.delete(`${this.apiUrl}/user`);
  }

  clearSession() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  }
}




