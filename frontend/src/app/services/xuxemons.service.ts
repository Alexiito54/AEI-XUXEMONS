import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Xuxemon {
  id: number;
  nombre: string;
  tipo: string;
  tamaño: string;
  imagen: string;
  atrapado?: boolean;
}

export interface XuxedexEntry {
  id: number;
  nombre: string;
  tipo: string;
  tamaño: string;
  imagen: string;
  atrapado: boolean;
  visto: boolean;
  oculto: boolean;
}

export interface XuxedexResponse {
  xuxemons: XuxedexEntry[];
  estadisticas: {
    total: number;
    atrapados: number;
    vistos: number;
    is_admin: boolean;
  };
}

export interface ColeccionXuxemon {
  id: number;
  user_id: number;
  xuxemon_id: number;
  capturado_en: string;
  xuxemon: Xuxemon;
}

@Injectable({
  providedIn: 'root'
})
export class XuxemonsService {
  private apiUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient) {}

  // Obtener todos los Xuxemons disponibles (público)
  getTodosXuxemons(): Observable<Xuxemon[]> {
    return this.http.get<Xuxemon[]>(`${this.apiUrl}/xuxemons`);
  }

  // Obtener datos de la Xuxedex (protegido)
  getXuxedex(): Observable<XuxedexResponse> {
    return this.http.get<XuxedexResponse>(`${this.apiUrl}/xuxedex`);
  }

  // Obtener Xuxemons capturados por el usuario (protegido)
  getColeccion(): Observable<ColeccionXuxemon[]> {
    return this.http.get<ColeccionXuxemon[]>(`${this.apiUrl}/colecciones`);
  }

  // Capturar un Xuxemon aleatorio (protegido)
  capturarXuxemon(): Observable<any> {
    return this.http.post(`${this.apiUrl}/colecciones`, {});
  }

  // Liberar un Xuxemon (protegido)
  liberarXuxemon(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/colecciones/${id}`);
  }
}
