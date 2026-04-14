import { Component, ViewEncapsulation, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { XuxemonsService } from '../services/xuxemons.service';

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
  xuxemonCapturados: number[] = [];
  isLoading = true;
  errorMsg = '';

  // Estado UI
  currentFilter: 'todos' | 'atrapado' | 'visto' | 'nuevo' = 'todos';
  currentSearch = '';

  selected?: Xuxemon;
  modalAbierto = false;

  constructor(private router: Router, private xuxemonsService: XuxemonsService) {}

  ngOnInit(): void {
    this.cargarXuxemons();
  }

  cargarXuxemons(): void {
    this.isLoading = true;
    // Cargar todos los Xuxemons disponibles
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
        // Cargar colección del usuario para saber cuáles están atrapados
        this.cargarColeccion();
      },
      error: (error: any) => {
        this.errorMsg = 'Error al cargar los Xuxemons';
        console.error('Error cargando Xuxemons:', error);
        this.isLoading = false;
      }
    });
  }

  cargarColeccion(): void {
    this.xuxemonsService.getColeccion().subscribe({
      next: (coleccion: any[]) => {
        // CORRECCIÓN: Usar push() en lugar de add() para permitir duplicados
        this.xuxemonCapturados = [];
        coleccion.forEach(item => {
          this.xuxemonCapturados.push(item.xuxemon_id);
        });
        // Marcar como atrapados en la lista
        this.xuxemons = this.xuxemons.map(x => ({
          ...x,
          atrapado: this.xuxemonCapturados.includes(x.id), // includes() en lugar de has()
          visto: this.xuxemonCapturados.includes(x.id),
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

  liberarXuxemon(id: number): void {
    if (confirm('¿Estás seguro de que quieres liberar este Xuxemon?')) {
      this.xuxemonsService.liberarXuxemon(id).subscribe({
        next: () => {
          console.log('Xuxemon liberado');
          this.cargarColeccion();
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
    this.modalAbierto = true;
  }

  cerrarModal(): void {
    this.modalAbierto = false;
    this.selected = undefined;
  }
}
