import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { XuxemonsService, ColeccionXuxemon } from '../services/xuxemons.service';
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

  // Usuario
  usuario: Usuario | null = null;
  isAdmin: boolean = false;

  // Jugador
  coleccion: ColeccionXuxemon[] = [];
  xuxemonsTotales: number = 0;
  xuxemonsAtrapados: number = 0;
  capturasTotales: number = 0;
  batallas: number = 0;
  amigos: number = 0;
  nivel: number = 1;
  cargando: boolean = true;

  // Admin — estadísticas
  totalJugadores: number = 0;
  totalXuxemonsCapturados: number = 0;
  totalEnfermos: number = 0;
  jugadoresRecientes: any[] = [];

  // Admin — modal ficha
  jugadorSeleccionado: any = null;
  coleccionJugador: any[] = [];
  modalAbierto: boolean = false;
  cargandoModal: boolean = false;

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
    this.usuario = this.authService.getStoredUser();
    this.isAdmin = this.authService.isAdmin();

    this.xuxemonsService.getColeccion().subscribe({
      next: (data) => {
        this.coleccion = data;
        this.capturasTotales = data.length;
        if (!this.isAdmin) {
          this.xuxemonsAtrapados = this.contarXuxemonsUnicos(data);
        }
        this.calcularNivel();
        this.cargando = false;
      },
      error: (err) => {
        console.error('Error al cargar colección:', err);
        this.cargando = false;
      }
    });

    this.xuxemonsService.getXuxedex().subscribe({
      next: (data) => {
        this.xuxemonsTotales = data.estadisticas.total;
        if (this.isAdmin) {
          this.xuxemonsAtrapados = this.xuxemonsTotales;
          this.cargando = false;
          this.cargarEstadisticasAdmin();
        }
      },
      error: (err) => {
        console.error('Error al cargar Xuxedex:', err);
        this.xuxemonsService.getTodosXuxemons().subscribe({
          next: (xuxemons) => {
            this.xuxemonsTotales = xuxemons.length;
            if (this.isAdmin) this.xuxemonsAtrapados = this.xuxemonsTotales;
          }
        });
      }
    });
  }

  cargarEstadisticasAdmin() {
    this.xuxemonsService.getAdminJugadores().subscribe({
      next: (response) => {
        this.jugadoresRecientes = response.jugadores;
        this.totalJugadores = response.jugadores.length;
        this.totalXuxemonsCapturados = response.jugadores
          .reduce((acc: number, j: any) => acc + (j.total_xuxemons || 0), 0);
      },
      error: (err) => console.error('Error cargando jugadores admin:', err)
    });
  }

  // Modal ficha jugador
  abrirFichaJugador(jugador: any) {
    this.jugadorSeleccionado = jugador;
    this.modalAbierto = true;
    this.cargandoModal = true;
    this.coleccionJugador = [];

    this.xuxemonsService.getColeccionJugador(jugador.id).subscribe({
      next: (data) => {
        this.coleccionJugador = data.coleccion;
        this.cargandoModal = false;
      },
      error: (err) => {
        console.error('Error cargando colección del jugador:', err);
        this.cargandoModal = false;
      }
    });
  }

  cerrarModal() {
    this.modalAbierto = false;
    this.jugadorSeleccionado = null;
    this.coleccionJugador = [];
  }

  getBarraProgreso(total: number): string {
    const porcentaje = Math.round((total / this.xuxemonsTotales) * 10);
    return '█'.repeat(porcentaje) + '░'.repeat(10 - porcentaje);
  }

  // Helpers
  calcularNivel() {
    this.nivel = Math.min(50, Math.floor(this.capturasTotales / 5) + 1);
  }

  private contarXuxemonsUnicos(coleccion: ColeccionXuxemon[]): number {
    return new Set(
      coleccion
        .map((item) => item.xuxemon?.id ?? item.xuxemon_id ?? item.id_xuxemon)
        .filter((id): id is number => typeof id === 'number')
    ).size;
  }

  getEquipo(): any[] {
  return this.coleccion.slice(0, 4);
}

  obtenerImagenXuxemon(item: any): string {
  const imagen = item.xuxemon?.imagen?.toString().trim();
  if (imagen) {
    if (/^https?:\/\//.test(imagen) || imagen.startsWith('/')) {
      return encodeURI(imagen);
    }
    return encodeURI(`/images/xuxemons/${imagen}`);
  }
  return this.obtenerImagenPorTipoTamaño(item.xuxemon?.tipo, item.tamaño_actual || item.tamano);
}

  private obtenerImagenPorTipoTamaño(tipo: string, tamaño: string): string {
    const size = (tamaño || '').toLowerCase();
    switch (tipo) {
      case 'Agua':
        if (size.includes('peque')) return '/images/xuxemons/Slime agua - 1.png';
        if (size.includes('med'))   return '/images/xuxemons/Dragon agua - 2.png';
        if (size.includes('gran'))  return '/images/xuxemons/Dragon agua - 3.png';
        return '/images/xuxemons/Slime agua - 1.png';
      case 'Tierra':
        if (size.includes('peque')) return '/images/xuxemons/Golem roca - 1.png';
        if (size.includes('med'))   return '/images/xuxemons/Golem roca - 2.png';
        if (size.includes('gran'))  return '/images/xuxemons/Golem roca - 3.png';
        return '/images/xuxemons/Golem roca - 1.png';
      case 'Aire':
        if (size.includes('peque')) return '/images/xuxemons/Cabra aire - 1.png';
        if (size.includes('med'))   return '/images/xuxemons/Cabra aire - 2.png';
        if (size.includes('gran'))  return '/images/xuxemons/Cabra fuego - 3.png';
        return '/images/xuxemons/Ave fuego - 1.png';
      default:
        return '/images/xuxemons/Dragon agua - 1.png';
    }
  }

  // Navegación
  navegarAlInicio()     { this.router.navigate(['/pagina-principal']); }
  navegarAlXuxedex()    { this.router.navigate(['/xuxedex']); }
  navegarAlInventario() { this.router.navigate(['/mochila']); }
  navegarAlPerfil()     { this.router.navigate(['/info-usuario']); }
}