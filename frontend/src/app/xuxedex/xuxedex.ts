import { Component, ViewEncapsulation, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import {
  AdminJugador,
  XuxemonsService,
  XuxedexResponse,
  XuxemonInstancia,
} from '../services/xuxemons.service';
import { MochilaService, MochilaItem } from '../services/mochila.service';

interface Xuxemon {
  id: number;
  nombre: string;
  tipo: string;
  tamano: string;
  imagen: string;
  cantidadCapturada: number;
  atrapado: boolean;
  visto: boolean;
  oculto: boolean;
  imagenMostrada: string;
  instancias: XuxemonInstancia[];
}

@Component({
  selector: 'xuxedex',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './xuxedex.html',
  styleUrls: ['./xuxedex.css'],
  encapsulation: ViewEncapsulation.None
})
export class Xuxedex implements OnInit {
  isAdmin = false;

  xuxemons: Xuxemon[] = [];
  xuxemonCapturados: number[] = [];
  isLoading = true;
  errorMsg = '';
  adminJugadores: AdminJugador[] = [];
  adminLoading = false;
  adminErrorMsg = '';
  adminFeedbackMsg = '';
  adminFeedbackType: 'success' | 'error' | '' = '';
  asignandoJugadorId: number | null = null;

  currentFilter: 'todos' | 'atrapado' | 'visto' | 'oculto' = 'todos';
  currentSearch = '';

  selected?: Xuxemon;
  modalAbierto = false;
  botonQueAbrioModal: HTMLElement | null = null;

  mochilaItems: MochilaItem[] = [];
  accionMensaje = '';
  accionError = '';
  accionCargando = false;
  vacunaSeleccionada: number | null = null;

  // Evolución
  mostrandoEvolucion = false;
  evolucionAntes = '';
  evolucionDespues = '';
  evolucionNombre = '';

  constructor(
    private router: Router,
    private xuxemonsService: XuxemonsService,
    private mochilaService: MochilaService
  ) {}

  ngOnInit(): void {
    this.cargarXuxemons();
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.modalAbierto) {
        this.cerrarModal();
      }
    });
  }

  cargarXuxemons(): void {
    this.isLoading = true;
    this.xuxemonsService.getXuxedex().subscribe({
      next: (response: XuxedexResponse) => {
        this.xuxemons = response.xuxemons.map(x => ({
          id: x.id,
          nombre: x.oculto ? '???' : x.nombre,
          tipo: x.oculto ? '???' : x.tipo,
          tamano: x.tamaño,
          imagen: this.obtenerImagenXuxemon(x),
          cantidadCapturada: x.cantidad_capturada || 0,
          imagenMostrada: this.obtenerImagenXuxemon(x),
          atrapado: x.atrapado,
          visto: x.visto,
          oculto: x.oculto,
          instancias: x.instancias || [],
        }));

        this.isAdmin = response.estadisticas.is_admin;
        if (this.isAdmin) {
          this.cargarJugadoresAdmin();
        } else {
          this.adminJugadores = [];
        }
        this.isLoading = false;
      },
      error: (error: any) => {
        this.errorMsg = 'Error al cargar la Xuxedex';
        console.error('Error cargando Xuxedex:', error);
        this.isLoading = false;
      }
    });
  }

  cargarJugadoresAdmin(): void {
    this.adminLoading = true;
    this.adminErrorMsg = '';
    this.xuxemonsService.getAdminJugadores().subscribe({
      next: (response) => {
        this.adminJugadores = response.jugadores;
        this.adminLoading = false;
      },
      error: (error: any) => {
        this.adminErrorMsg = 'No se pudo cargar la lista de jugadores';
        console.error('Error cargando jugadores admin:', error);
        this.adminLoading = false;
      }
    });
  }

  asignarXuxemonAleatorio(jugador: AdminJugador): void {
    this.adminFeedbackMsg = '';
    this.adminFeedbackType = '';
    this.asignandoJugadorId = jugador.id;

    this.xuxemonsService.asignarXuxemonAleatorioAJugador(jugador.id).subscribe({
      next: (response) => {
        this.adminJugadores = this.adminJugadores.map((item) =>
          item.id === jugador.id ? response.jugador : item
        );
        this.adminFeedbackMsg = `${response.xuxemon.nombre} ha sido asignado a ${response.jugador.name}.`;
        this.adminFeedbackType = 'success';
        this.asignandoJugadorId = null;
      },
      error: (error: any) => {
        this.adminFeedbackMsg =
          error?.error?.message || 'No se pudo asignar el Xuxemon aleatorio';
        this.adminFeedbackType = 'error';
        this.asignandoJugadorId = null;
      }
    });
  }

  private obtenerImagenXuxemon(x: any): string {
    const imagen = x.imagen?.toString().trim();
    if (imagen) {
      if (/^https?:\/\//.test(imagen) || imagen.startsWith('/')) {
        return encodeURI(imagen);
      }
      return encodeURI(`/images/xuxemons/${imagen}`);
    }
    return this.obtenerImagenPorTipoTamano(x.tipo, x.tamaño || x.tamano);
  }

  private obtenerImagenDesdeNombre(nombreArchivo: string): string {
    return encodeURI(`/images/xuxemons/${nombreArchivo}`);
  }

  private obtenerImagenPorTipoTamano(tipo: string, tamano: string): string {
    const size = (tamano || '').toLowerCase();
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

  cargarColeccion(): void {
    this.xuxemonsService.getColeccion().subscribe({
      next: (coleccion: any[]) => {
        const mapa = new Map<number, any>();
        coleccion.forEach(item => {
          const xuxemonId = item.xuxemon_id || item.id_xuxemon;
          mapa.set(xuxemonId, item);
        });
        this.xuxemons = this.xuxemons.map(x => ({
          ...x,
          atrapado: mapa.has(x.id),
          visto: mapa.has(x.id),
        }));
        this.isLoading = false;
      },
      error: (error: any) => {
        console.warn('No autenticado o error cargando colección:', error);
        this.isLoading = false;
      }
    });
  }

  navegarAlInicio()     { this.router.navigate(['/pagina-principal']); }
  navegarAlXuxedex()    { this.router.navigate(['/xuxedex']); }
  navegarAlInventario() { this.router.navigate(['/mochila']); }
  navegarAlPerfil()     { this.router.navigate(['/info-usuario']); }

  get totalCount(): number  { return this.xuxemons.length; }
  get caughtCount(): number { return this.xuxemons.filter(x => x.atrapado).length; }
  get seenCount(): number   { return this.xuxemons.filter(x => x.visto).length; }

  get vacunasEnMochila(): MochilaItem[] {
    return this.mochilaItems.filter(m => m.item.tipo === 'vacuna');
  }

  get tieneXuxes(): boolean {
    return this.mochilaItems.some(m => m.item.tipo === 'xuxe' && m.cantidad > 0);
  }

  porcentajeHambre(inst: XuxemonInstancia): number {
    const max = inst.tamano_actual === 'Pequeño' ? 3 : 5;
    return Math.min(100, Math.round((inst.alimentaciones_pendientes / max) * 100));
  }

  claseBarraHambre(inst: XuxemonInstancia): string {
    const pct = this.porcentajeHambre(inst);
    if (pct >= 100) return 'llena';
    if (pct >= 60)  return 'casi-llena';
    return '';
  }

  setFilter(filter: 'todos' | 'atrapado' | 'visto' | 'oculto'): void {
    this.currentFilter = filter;
  }

  onSearchChange(value: string): void {
    this.currentSearch = value.trim().toLowerCase();
  }

  get listFiltrada(): Xuxemon[] {
    return this.xuxemons.filter(x => {
      const texto = this.currentSearch;
      const matchesSearch =
        !texto ||
        x.nombre.toLowerCase().includes(texto) ||
        x.id.toString().includes(texto);

      if (!matchesSearch) return false;

      switch (this.currentFilter) {
        case 'atrapado': return x.atrapado === true;
        case 'visto':    return x.visto === true && !x.atrapado;
        case 'oculto':   return x.oculto === true;
        default:         return true;
      }
    });
  }

  capturarXuxemon(): void {
    this.xuxemonsService.capturarXuxemon().subscribe({
      next: (result: any) => {
        console.log('Xuxemon capturado:', result);
        this.cargarXuxemons();
      },
      error: (error: any) => {
        console.error('Error capturando Xuxemon:', error);
      }
    });
  }

  liberarXuxemon(id: number): void {
    if (confirm('¿Estás seguro de que quieres liberar este Xuxemon?')) {
      this.xuxemonsService.liberarXuxemon(id).subscribe({
        next: () => {
          console.log('Xuxemon liberado');
          this.cargarXuxemons();
        },
        error: (error: any) => {
          console.error('Error liberando Xuxemon:', error);
        }
      });
    }
  }

  alimentarXuxemon(inst: XuxemonInstancia): void {
    this.accionMensaje = '';
    this.accionError = '';
    this.accionCargando = true;

    this.xuxemonsService.alimentarXuxemon(inst.coleccion_id).subscribe({
      next: (res: any) => {
        this.accionCargando = false;

              if (res.subio_nivel && res.xuxemon_evolucionado) {
        this.evolucionAntes    = this.selected?.imagenMostrada || '';
        this.evolucionDespues  = this.obtenerImagenDesdeNombre(res.xuxemon_evolucionado.imagen);
        this.evolucionNombre   = res.xuxemon_evolucionado.nombre;
        this.mostrandoEvolucion = true;

        setTimeout(() => {
          this.mostrandoEvolucion = false;
          this.accionMensaje = `¡${res.xuxemon_evolucionado.nombre} ha evolucionado!`;
          // Cerrar modal y recargar para que muestre el xuxemon nuevo
          this.cerrarModal();
          this.cargarXuxemons();
        }, 3200);
      } else {
        this.accionMensaje = res.message || 'Alimentado correctamente';
        this.cargarXuxemons();
      }

        this.mochilaService.getMochila().subscribe(data => {
          this.mochilaItems = data;
        });

       if (this.selected) {
  const idx = this.selected.instancias.findIndex(i => i.coleccion_id === inst.coleccion_id);
  if (idx !== -1) {
    this.selected.instancias[idx] = {
      ...this.selected.instancias[idx],
      tamano_actual:             res.tamano_actual,
      nivel:                     res.nivel,
      alimentaciones_pendientes: res.coleccion.alimentaciones_pendientes,
      esta_enfermo:              res.enfermedades?.length > 0,
      enfermedades:              res.enfermedades || [],
    };
  }
}
      },
      error: (err: any) => {
        this.accionError = err.error?.message || 'Error al alimentar';
        this.accionCargando = false;
      }
    });
  }

  curarXuxemon(inst: XuxemonInstancia): void {
    if (!this.vacunaSeleccionada) {
      this.accionError = 'Selecciona una vacuna';
      return;
    }

    this.accionMensaje = '';
    this.accionError = '';
    this.accionCargando = true;

    this.xuxemonsService.curarXuxemon(inst.coleccion_id, this.vacunaSeleccionada).subscribe({
      next: (res: any) => {
        this.accionMensaje = res.message || 'Curado correctamente';
        this.accionCargando = false;
        this.vacunaSeleccionada = null;
        this.mochilaService.getMochila().subscribe(data => {
          this.mochilaItems = data;
        });
        if (this.selected) {
  const idx = this.selected.instancias.findIndex(i => i.coleccion_id === inst.coleccion_id);
  if (idx !== -1) {
    this.selected.instancias[idx] = {
      ...this.selected.instancias[idx],
      tamano_actual: res.tamano_actual,
      nivel: res.nivel,
      alimentaciones_pendientes: res.coleccion?.alimentaciones_pendientes ?? this.selected.instancias[idx].alimentaciones_pendientes,
      esta_enfermo: res.enfermedades?.length > 0,
      enfermedades: res.enfermedades || [],
    };
  }
}
      },
      error: (err: any) => {
        this.accionError = err.error?.message || 'Error al curar';
        this.accionCargando = false;
      }
    });
  }

  abrirModal(x: Xuxemon): void {
  this.botonQueAbrioModal = document.activeElement as HTMLElement;  
  this.selected = x;
  this.modalAbierto = true;
  this.accionMensaje = '';
  this.accionError = '';
  this.vacunaSeleccionada = null;
  this.mostrandoEvolucion = false;

  setTimeout(() => {
    const modal = document.querySelector('.modal-box') as HTMLElement;
    modal?.focus();
  }, 50);

  this.mochilaService.getMochila().subscribe({
    next: (data) => { this.mochilaItems = data; },
    error: () => { this.mochilaItems = []; }
  });
}


  cerrarModal(): void {
    this.modalAbierto = false;
    this.selected = undefined;
    setTimeout(() => {
      this.botonQueAbrioModal?.focus();
    }, 50);
  }
}
