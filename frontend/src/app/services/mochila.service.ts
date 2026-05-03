import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface MochilaItem {
  id: number;
  id_usuario: number;  
  id_item: number;     
  cantidad: number;
  slot: number;
  item: {
    id: number;
    nombre: string;
    tipo: 'xuxe' | 'vacuna';
    icono: string;
    apilable: boolean;
  };
}

export interface Item {
  id: number;
  nombre: string;
  tipo: 'xuxe' | 'vacuna';
  icono: string;
  apilable: boolean;
}

@Injectable({
  providedIn: 'root'
})
export class MochilaService {
  private apiUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient) {}

  private getHeaders(): HttpHeaders {
    const token = localStorage.getItem('token') || sessionStorage.getItem('token');
    
    let headers = new HttpHeaders({
      'Content-Type': 'application/json'
    });
    
    if (token) {
      headers = headers.set('Authorization', `Bearer ${token}`);
    }
    
    return headers;
  }

  getMochila(): Observable<MochilaItem[]> {
    return this.http.get<MochilaItem[]>(`${this.apiUrl}/mochila`, {
      headers: this.getHeaders()
    });
  }

  getItems(): Observable<Item[]> {
    return this.http.get<Item[]>(`${this.apiUrl}/items`, {
      headers: this.getHeaders()
    });
  }

  añadirItem(item_id: number, cantidad: number, id_usuario: number): Observable<any> {
    return this.http.post(`${this.apiUrl}/mochila`, { id_item: item_id, cantidad, id_usuario }, {
      headers: this.getHeaders()
    });
  }

  eliminarItem(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/mochila/${id}`, {
      headers: this.getHeaders()
    });
  }
}
