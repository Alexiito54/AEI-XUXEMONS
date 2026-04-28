import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { XuxemonsService, ColeccionXuxemon, XuxedexResponse } from '../services/xuxemons.service';
import { AuthService } from '../auth.service';

interface Usuario {
  id: number;
  name: string;
  email: string;
  rol: string;
}

@Component({
  selector: 'app-pagina-principal',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './pagina-principal.html',
  styleUrls: ['./pagina-principal.css'],
  encapsulation: ViewEncapsulation.None
})
export class PaginaPrincipal implements OnInit {
  usuario: Usuario | null = null;
  coleccion: ColeccionXuxemon[] = [];
  xuxemonsTotales: number = 0;
  xuxemonsAtrapados: number = 0;
  batallas: number = 0;
  amigos: number = 0;
  nivel: number = 1;
  cargando: boolean = true;

  constructor(
    private router: Router,
    private xuxemonsService: XuxemonsService,
    private authService: AuthService
  ) {}

  ngOnInit() {
    this.cargarDatos();
  }

  cargarDatos() {
    this.cargando = true;
    
    // Obtener datos del usuario
    this.usuario = this.authService.getStoredUser();
    
    // Cargar colección de Xuxemons
    this.xuxemonsService.getColeccion().subscribe({
      next: (data) => {
        this.coleccion = data;
        this.xuxemonsAtrapados = data.length;
        this.calcularNivel();
        this.cargando = false;
      },
      error: (err) => {
        console.error('Error al cargar colección:', err);
        this.cargando = false;
      }
    });

    // Cargar Xuxedex para obtener total de Xuxemons disponibles
    this.xuxemonsService.getXuxedex().subscribe({
      next: (data) => {
        this.xuxemonsTotales = data.estadisticas.total;
      },
      error: (err) => {
        console.error('Error al cargar Xuxedex:', err);
        // Si falla, intentamos obtener el total de otra forma
        this.xuxemonsService.getTodosXuxemons().subscribe({
          next: (xuxemons) => {
            this.xuxemonsTotales = xuxemons.length;
          }
        });
      }
    });
  }

  calcularNivel() {
    // El nivel se calcula basado en los Xuxemons atrapados
    // Cada 5 Xuxemons = 1 nivel, máximo nivel 50
    this.nivel = Math.min(50, Math.floor(this.xuxemonsAtrapados / 5) + 1);
  }

  getEquipo(): ColeccionXuxemon[] {
    // Retorna hasta 4 Xuxemons del equipo (los primeros 4 de la colección)
    return this.coleccion.slice(0, 4);
  }

  // Navegación
  navegarAlInicio() {
    this.router.navigate(['/pagina-principal']);
  }

  navegarAlXuxedex() {
    this.router.navigate(['/xuxedex']);
  }

  navegarAlInventario() {
    this.router.navigate(['/mochila']);
  }

  navegarAlAmigos() {
    this.router.navigate(['/amigos']);
  }

  navegarAlBatalla() {
    this.router.navigate(['/batalla']);
  }

  navegarAlChat() {
    this.router.navigate(['/chat']);
  }

  navegarAlPerfil() {
    this.router.navigate(['/info-usuario']);
  }
  
  navegarAlAdmin() {
    this.router.navigate(['/admin']);
  }
}
