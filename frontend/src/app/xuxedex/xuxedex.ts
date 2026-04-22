import { Component, ViewEncapsulation, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { XuxemonsService, XuxedexEntry, XuxedexResponse } from '../services/xuxemons.service';

interface Xuxemon {
  id: number;
  nombre: string;
  tipo: string;
  tamano: string;
  imagen: string;
  atrapado: boolean;
  visto: boolean;
  oculto: boolean;
  imagenMostrada: string; // Para mostrar imagen normal u oculta
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
  currentFilter: 'todos' | 'atrapado' | 'visto' | 'oculto' = 'todos';
  currentSearch = '';

  selected?: Xuxemon;
  modalAbierto = false;

  constructor(private router: Router, private xuxemonsService: XuxemonsService) {}

  ngOnInit(): void {
    this.cargarXuxemons();
  }

  cargarXuxemons(): void {
    this.isLoading = true;
    // Cargar datos de la Xuxedex (incluye estado de cada Xuxemon)
    this.xuxemonsService.getXuxedex().subscribe({
      next: (response: XuxedexResponse) => {
        this.xuxemons = response.xuxemons.map(x => ({
          id: x.id,
          nombre: x.oculto ? '???' : x.nombre,
          tipo: x.oculto ? '???' : x.tipo,
          tamano: x.tamaño,
          imagen: this.obtenerImagenXuxemon(x),
          imagenMostrada: x.oculto ? '' : this.obtenerImagenXuxemon(x), // Imagen vacía para ocultos
          atrapado: x.atrapado,
          visto: x.visto,
          oculto: x.oculto,
        }));

        // Actualizar estadísticas
        this.isAdmin = response.estadisticas.is_admin;
        this.isLoading = false;
      },
      error: (error: any) => {
        this.errorMsg = 'Error al cargar la Xuxedex';
        console.error('Error cargando Xuxedex:', error);
        this.isLoading = false;
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

    return this.obtenerImagenPorTipoTamaño(x.tipo, x.tamaño || x.tamano);
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
        this.cargarXuxemons(); // Cambiar a cargarXuxemons()
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
          this.cargarXuxemons(); // Cambiar a cargarXuxemons()
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
