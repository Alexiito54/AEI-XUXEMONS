import { Component, ViewEncapsulation } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

interface Xuxemon {
  id: number;
  nombre: string;
  tipos: string[];
  descripcion: string;
  sprite: string;
  atrapado: boolean;
  visto: boolean;
  nuevo: boolean;
}

@Component({
  selector: 'xuxedex',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './xuxedex.html',
  styleUrls: ['./xuxedex.css'],
  encapsulation: ViewEncapsulation.None
})
export class Xuxedex {
  // Simulación de usuario actual y rol
  isAdmin = false; // Cambia a true para probar modo admin

  // Datos mock
  xuxemons: Xuxemon[] = [
    {
      id: 1,
      nombre: 'Xuxemonchu',
      tipos: ['#'],
      descripcion: '#.',
      sprite: '#',
      atrapado: true,
      visto: true,
      nuevo: false,
    },
    {
      id: 2,
      nombre: 'Negrosaurio',
      tipos: ['#'],
      descripcion: '#',
      sprite: '#',
      atrapado: false,
      visto: true,
      nuevo: true,
    },
    {
      id: 3,
      nombre: 'Lleidamon',
      tipos: ['#', '#'],
      descripcion: '#',
      sprite: '#',
      atrapado: false,
      visto: false,
      nuevo: true,
    },
  ];

  // Estado UI
  currentFilter: 'todos' | 'atrapado' | 'visto' | 'nuevo' = 'todos';
  currentSearch = '';

  selected?: Xuxemon;
  modalAbierto = false;

  constructor(private router: Router) {}

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
        case 'atrapado': return x.atrapado;
        case 'visto':    return x.visto;
        case 'nuevo':    return x.nuevo;
        default:         return true;
      }
    });
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

  // Stats de ejemplo
  get statsEjemplo(): { label: string; valor: number }[] {
    if (!this.selected) return [];
    const base = this.selected.id;
    return [
      { label: 'ATAQUE', valor: 50 + base },
      { label: 'DEFENSA', valor: 40 + base },
      { label: 'VELOCIDAD', valor: 30 + base },
    ];
  }
}
