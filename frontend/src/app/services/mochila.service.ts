import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface MochilaItem {
  id: number;
  user_id: number;
  item_id: number;
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

@Injectable({
  providedIn: 'root'
})
export class MochilaService {
  private apiUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient) {}

  getMochila(): Observable<MochilaItem[]> {
    return this.http.get<MochilaItem[]>(`${this.apiUrl}/mochila`);
  }

  añadirItem(item_id: number, cantidad: number, user_id: number): Observable<any> {
    return this.http.post(`${this.apiUrl}/mochila`, { item_id, cantidad, user_id });
  }

  eliminarItem(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/mochila/${id}`);
  }
}
