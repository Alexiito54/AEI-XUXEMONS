import { Component, ViewEncapsulation, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { XuxemonsService, ColeccionXuxemon, Vacuna, Enfermedad } from '../services/xuxemons.service';
import { MochilaService, MochilaItem } from '../services/mochila.service';

interface Xuxemon {
  id: number;
  nombre: string;
  tipo: string;
  tamano: string;
  imagen: string;
  atrapado: boolean;
  visto?: boolean;
  nuevo?: boolean;
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
  // Simulación de usuario actual y rol
  isAdmin = false; // Cambia a true para probar modo admin

  // Datos desde la API
  xuxemons: Xuxemon[] = [];
  xuxemonCapturados: Map<number, ColeccionXuxemon> = new Map();
  coleccionCompleta: ColeccionXuxemon[] = [];
  vacunas: Vacuna[] = [];
  mochilaItems: MochilaItem[] = [];
  
  isLoading = true;
  errorMsg = '';

  // Estado UI
  currentFilter: 'todos' | 'atrapado' | 'visto' | 'nuevo' = 'todos';
  currentSearch = '';

  selected?: Xuxemon;
  selectedColeccion?: ColeccionXuxemon;
  modalAbierto = false;
  
  // Estado de acciones
  alimentando = false;
  curando = false;
  alimentarMsg = '';
  curarMsg = '';
  vacunaSeleccionada = 0;

  constructor(
    private router: Router, 
    private xuxemonsService: XuxemonsService,
    private mochilaService: MochilaService
  ) {}

  ngOnInit(): void {
    this.cargarDatos();
  }

  cargarDatos(): void {
    this.isLoading = true;
    // Cargar en paralelo todos los Xuxemons y la colección del usuario
    this.xuxemonsService.getTodosXuxemons().subscribe({
      next: (xuxemons: any[]) => {
        this.xuxemons = xuxemons.map(x => ({
          id: x.id,
          nombre: x.nombre,
          tipo: x.tipo,
          tamano: x.tamaño || x.tamano || 'Normal',
          imagen: x.imagen,
          atrapado: false,
          visto: false,
          nuevo: false
        }));
        this.cargarColeccion();
      },
      error: (error: any) => {
        this.errorMsg = 'Error al cargar los Xuxemons';
        console.error('Error cargando Xuxemons:', error);
        this.isLoading = false;
      }
    });

    // Cargar vacunas y mochila en paralelo
    this.xuxemonsService.getVacunas().subscribe({
      next: (vacunas) => {
        this.vacunas = vacunas;
      },
      error: (error) => {
        console.error('Error cargando vacunas:', error);
      }
    });

    this.mochilaService.getMochila().subscribe({
      next: (mochila) => {
        this.mochilaItems = mochila;
      },
      error: (error) => {
        console.error('Error cargando mochila:', error);
      }
    });
  }

  cargarColeccion(): void {
    this.xuxemonsService.getColeccion().subscribe({
      next: (coleccion: any[]) => {
        this.coleccionCompleta = coleccion;
        this.xuxemonCapturados.clear();
        coleccion.forEach(item => {
          this.xuxemonCapturados.set(item.xuxemon_id, item);
        });
        // Marcar como atrapados en la lista
        this.xuxemons = this.xuxemons.map(x => ({
          ...x,
          atrapado: this.xuxemonCapturados.has(x.id),
          visto: this.xuxemonCapturados.has(x.id),
          nuevo: false
        }));
        this.isLoading = false;
      },
      error: (error: any) => {
        // Si no está autenticado, mostrar todos como no atrapados
        console.warn('No autenticado o error cargando colección:', error);
        this.isLoading = false;
      }
    });
  }

  // Obtener item de la mochila del tipo que necesita el xuxemon
  get tieneXuxeParaAlimental(): boolean {
    return this.mochilaItems.some(m => m.item.tipo === 'xuxe' && m.cantidad > 0);
  }

  // Obtener vacunas disponibles en la mochila
  get vacunasDisponibles(): MochilaItem[] {
    return this.mochilaItems.filter(m => m.item.tipo === 'vacuna' && m.cantidad > 0);
  }

  // Obtener ID de mochila del primer xuxe disponible
  get idXuxeParaAlimental(): number {
    const xuxe = this.mochilaItems.find(m => m.item.tipo === 'xuxe' && m.cantidad > 0);
    return xuxe?.id || 0;
  }

  // Navegación
  navegarAlInicio() { this.router.navigate(['/pagina-principal']); }
  navegarAlXuxedex() { this.router.navigate(['/xuxedex']); }
  navegarAlInventario() { this.router.navigate(['/mochila']); }
  navegarAlAmigos() { this.router.navigate(['/amigos']); }
  navegarAlBatalla() { this.router.navigate(['/batalla']); }
  navegarAlChat() { this.router.navigate(['/chat']); }
  navegarAlPerfil() { this.router.navigate(['/info-usuario']); }
  navegarAlAdmin() { this.router.navigate(['/admin']); }

  // Getters para contadores
  get totalCount(): number {
    return this.xuxemons.length;
  }

  get caughtCount(): number {
    return this.xuxemons.filter(x => x.atrapado).length;
  }

  get seenCount(): number {
    return this.xuxemons.filter(x => x.visto).length;
  }

  // Filtro + búsqueda
  setFilter(filter: 'todos' | 'atrapado' | 'visto' | 'nuevo'): void {
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
        case 'visto':    return x.visto === true;
        case 'nuevo':    return x.nuevo === true;
        default:         return true;
      }
    });
  }

  capturarXuxemon(): void {
    this.xuxemonsService.capturarXuxemon().subscribe({
      next: (result: any) => {
        console.log('Xuxemon capturado:', result);
        this.cargarColeccion();
      },
      error: (error: any) => {
        console.error('Error capturando Xuxemon:', error);
      }
    });
  }

  alimentarXuxemon(): void {
    if (!this.selectedColeccion) return;

    this.alimentando = true;
    this.alimentarMsg = '';

    this.xuxemonsService.alimentarXuxemon(this.selectedColeccion.id).subscribe({
      next: (result: any) => {
        this.alimentarMsg = '✅ ' + (result.message || 'Xuxemon alimentado con éxito');
        this.cargarColeccion();
        this.cargarDatos(); // Recargar mochila también
        this.alimentando = false;
        setTimeout(() => {
          this.alimentarMsg = '';
        }, 3000);
      },
      error: (error: any) => {
        this.alimentarMsg = '❌ ' + (error.error?.message || 'Error al alimentar');
        this.alimentando = false;
      }
    });
  }

  curarXuxemon(): void {
    if (!this.selectedColeccion || !this.vacunaSeleccionada) {
      this.curarMsg = '❌ Selecciona una vacuna';
      return;
    }

    this.curando = true;
    this.curarMsg = '';

    this.xuxemonsService.curarXuxemon(this.selectedColeccion.id, this.vacunaSeleccionada).subscribe({
      next: (result: any) => {
        this.curarMsg = '✅ ' + (result.message || 'Xuxemon curado con éxito');
        this.cargarColeccion();
        this.cargarDatos(); // Recargar mochila también
        this.curando = false;
        this.vacunaSeleccionada = 0;
        setTimeout(() => {
          this.curarMsg = '';
        }, 3000);
      },
      error: (error: any) => {
        this.curarMsg = '❌ ' + (error.error?.message || 'Error al curar');
        this.curando = false;
      }
    });
  }

  liberarXuxemon(id: number): void {
    if (confirm('¿Estás seguro de que quieres liberar este Xuxemon?')) {
      this.xuxemonsService.liberarXuxemon(id).subscribe({
        next: () => {
          console.log('Xuxemon liberado');
          this.cargarColeccion();
          this.cerrarModal();
        },
        error: (error: any) => {
          console.error('Error liberando Xuxemon:', error);
        }
      });
    }
  }

  // Modal
  abrirModal(x: Xuxemon): void {
    this.selected = x;
    this.selectedColeccion = this.xuxemonCapturados.get(x.id);
    this.modalAbierto = true;
    this.alimentarMsg = '';
    this.curarMsg = '';
    this.vacunaSeleccionada = 0;
  }

  cerrarModal(): void {
    this.modalAbierto = false;
    this.selected = undefined;
    this.selectedColeccion = undefined;
  }
}
