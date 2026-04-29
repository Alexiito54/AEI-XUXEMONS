import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Xuxemon {
  id: number;
  nombre: string;
  tipo: string;
  tamano: string;
  imagen: string;
  atrapado?: boolean;
}

export interface Enfermedad {
  id: number;
  nombre: string;
  descripcion: string;
  xuxes_extra_para_crecer: number;
  impide_alimentacion: boolean;
  porcentaje_infeccion: number;
}

export interface Vacuna {
  id: number;
  nombre: string;
  cura_enfermedad_id: number | null;
}

export interface ColeccionXuxemon {
  id: number;
  user_id: number;
  xuxemon_id: number;
  tamano_actual: string;
  nivel: number;
  alimentaciones_pendientes: number;
  capturado_en?: string;
  xuxemon: Xuxemon;
  enfermedades?: Enfermedad[];
}

@Injectable({
  providedIn: 'root'
})
export class XuxemonsService {
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

  // Obtener todos los Xuxemons disponibles (público)
  getTodosXuxemons(): Observable<Xuxemon[]> {
    return this.http.get<Xuxemon[]>(`${this.apiUrl}/xuxemons`);
  }

  // Obtener Xuxemons capturados por el usuario (protegido)
  getColeccion(): Observable<ColeccionXuxemon[]> {
    return this.http.get<ColeccionXuxemon[]>(`${this.apiUrl}/colecciones`, {
      headers: this.getHeaders()
    });
  }

  // Obtener un Xuxemon capturado específico
  getColeccionPorId(id: number): Observable<ColeccionXuxemon> {
    return this.http.get<ColeccionXuxemon>(`${this.apiUrl}/colecciones/${id}`, {
      headers: this.getHeaders()
    });
  }

  // Capturar un Xuxemon aleatorio (protegido)
  capturarXuxemon(): Observable<any> {
    return this.http.post(`${this.apiUrl}/colecciones`, {}, {
      headers: this.getHeaders()
    });
  }

  // Alimentar un Xuxemon (protegido)
  alimentarXuxemon(id: number): Observable<any> {
    return this.http.post(`${this.apiUrl}/colecciones/${id}/alimentar`, {}, {
      headers: this.getHeaders()
    });
  }

  curarXuxemon(id: number, itemId: number): Observable<any> {
    return this.http.post(`${this.apiUrl}/colecciones/${id}/curar`, {
        id_item: itemId  
    }, {
        headers: this.getHeaders()
    });
}

  // Liberar un Xuxemon (protegido)
  liberarXuxemon(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/colecciones/${id}`, {
      headers: this.getHeaders()
    });
  }

  // Obtener todas las vacunas disponibles
  getVacunas(): Observable<Vacuna[]> {
    return this.http.get<Vacuna[]>(`${this.apiUrl}/vacunas`);
  }

  // Obtener todas las enfermedades
  getEnfermedades(): Observable<Enfermedad[]> {
    return this.http.get<Enfermedad[]>(`${this.apiUrl}/enfermedades`);
  }
}
