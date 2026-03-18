import { Component, ViewEncapsulation } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

interface MochilaItem {
  id: number;
  nombre: string;
  tipo: 'xuxe' | 'vacuna';
  icono: string;
  cantidad: number; // Para apilables, siempre 1 para no apilables
  apilable: boolean;
}

@Component({
  selector: 'app-mochila',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './mochila.component.html',
  styleUrls: ['./mochila.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class MochilaComponent {
  // Simulación de usuario actual y rol
  isAdmin = false; // Cambia a true para probar modo admin

  // Tipos de xuxes apilables
  tiposXuxes = [
    { nombre: 'Xuxe Roja', icono: '🍬', id: 1 },
    { nombre: 'Xuxe Blava', icono: '🍭', id: 2 },
    { nombre: 'Xuxe Verda', icono: '🍡', id: 3 }
  ];

  // Tipos de vacunas (no apilables)
  tiposVacunas = [
    { nombre: 'Vacuna A', icono: '💉', id: 101 },
    { nombre: 'Vacuna B', icono: '💊', id: 102 }
  ];

  // Mochila del usuario (simulada)
  mochila: MochilaItem[] = [
    { id: 1, nombre: 'Xuxe Roja', tipo: 'xuxe', icono: '🍬', cantidad: 5, apilable: true },
    { id: 1, nombre: 'Xuxe Roja', tipo: 'xuxe', icono: '🍬', cantidad: 2, apilable: true },
    { id: 2, nombre: 'Xuxe Blava', tipo: 'xuxe', icono: '🍭', cantidad: 4, apilable: true },
    { id: 101, nombre: 'Vacuna A', tipo: 'vacuna', icono: '💉', cantidad: 1, apilable: false },
    { id: 102, nombre: 'Vacuna B', tipo: 'vacuna', icono: '💊', cantidad: 1, apilable: false }
  ];

  get slots() {
    // Devuelve un array de 20 espacios, rellenando con null los vacíos
    const items = [...this.mochila];
    while (items.length < 20) items.push(null as any);
    return items.slice(0, 20);
  }

  // Métodos de navegación (igual que antes)
  constructor(private router: Router) {}
  navegarAlInicio() { this.router.navigate(['/pagina-principal']); }
  navegarAlXuxedex() { this.router.navigate(['/xuxedex']); }
  navegarAlInventario() { this.router.navigate(['/mochila']); }
  navegarAlAmigos() { this.router.navigate(['/amigos']); }
  navegarAlBatalla() { this.router.navigate(['/batalla']); }
  navegarAlChat() { this.router.navigate(['/chat']); }
  navegarAlPerfil() { this.router.navigate(['/info-usuario']); }
  navegarAlAdmin() { this.router.navigate(['/admin']); }

  // ADMIN: Simulación de añadir xuxes a la mochila
  adminXuxeId = 1;
  adminCantidad = 1;
  adminError = '';
  adminAddXuxes() {
    this.adminError = '';
    let cantidad = this.adminCantidad;
    const tipo = this.tiposXuxes.find(x => x.id === +this.adminXuxeId);
    if (!tipo) return;
    // Agrupa en stacks de 5
    let espaciosLibres = 20 - this.mochila.length;
    while (cantidad > 0 && espaciosLibres > 0) {
      const stack = Math.min(5, cantidad);
      this.mochila.push({
        id: tipo.id,
        nombre: tipo.nombre,
        tipo: 'xuxe',
        icono: tipo.icono,
        cantidad: stack,
        apilable: true
      });
      cantidad -= stack;
      espaciosLibres--;
    }
    if (cantidad > 0) this.adminError = 'No hay espacio suficiente, se han descartado ' + cantidad + ' xuxes.';
  }
}

