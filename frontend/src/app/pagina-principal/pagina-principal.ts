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

  obtenerImagenXuxemon(xuxemon: any): string {
    const imagen = xuxemon.xuxemon?.imagen?.toString().trim();
    if (imagen) {
      if (/^https?:\/\//.test(imagen) || imagen.startsWith('/')) {
        return encodeURI(imagen);
      }
      return encodeURI(`/images/xuxemons/${imagen}`);
    }

    // Si no hay imagen, usar una por defecto según el tipo
    return this.obtenerImagenPorTipoTamaño(xuxemon.xuxemon?.tipo, xuxemon.tamano);
  }

  private obtenerImagenPorTipoTamaño(tipo: string, tamaño: string): string {
    const size = (tamaño || '').toLowerCase();
    switch (tipo) {
      case 'Agua':
        if (size.includes('peque')) return '/images/xuxemons/Slime agua - 1.png';
        if (size.includes('med')) return '/images/xuxemons/Dragon agua - 2.png';
        if (size.includes('gran')) return '/images/xuxemons/Dragon agua - 3.png';
        return '/images/xuxemons/Slime agua - 1.png';
      case 'Tierra':
        if (size.includes('peque')) return '/images/xuxemons/Golem roca - 1.png';
        if (size.includes('med')) return '/images/xuxemons/Golem roca - 2.png';
        if (size.includes('gran')) return '/images/xuxemons/Golem roca - 3.png';
        return '/images/xuxemons/Golem roca - 1.png';
      case 'Aire':
        if (size.includes('peque')) return '/images/xuxemons/Cabra aire - 1.png';
        if (size.includes('med')) return '/images/xuxemons/Cabra aire - 2.png';
        if (size.includes('gran')) return '/images/xuxemons/Cabra fuego - 3.png';
        return '/images/xuxemons/Ave fuego - 1.png';
      default:
        return '/images/xuxemons/Dragon agua - 1.png';
    }
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
