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

  getMochila(): Observable<MochilaItem[]> {
    return this.http.get<MochilaItem[]>(`${this.apiUrl}/mochila`);
  }

  getItems(): Observable<Item[]> {
    return this.http.get<Item[]>(`${this.apiUrl}/items`);
  }

  añadirItem(item_id: number, cantidad: number, id_usuario: number): Observable<any> {
    return this.http.post(`${this.apiUrl}/mochila`, { id_item: item_id, cantidad, id_usuario });
  }

  eliminarItem(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/mochila/${id}`);
  }
}
