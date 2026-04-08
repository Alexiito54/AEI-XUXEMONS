import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MochilaService, MochilaItem } from '../services/mochila.service';

@Component({
  selector: 'app-mochila',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './mochila.component.html',
  styleUrls: ['./mochila.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class MochilaComponent implements OnInit {
  isAdmin = localStorage.getItem('role') === 'ADMIN';

  tiposXuxes = [
    { nombre: 'Xuxe Roja',  icono: '🍬', id: 1 },
    { nombre: 'Xuxe Blava', icono: '🍭', id: 2 },
    { nombre: 'Xuxe Verda', icono: '🍡', id: 3 }
  ];

  mochila: MochilaItem[] = [];
  cargando = true;
  error = '';

  adminXuxeId = 1;
  adminCantidad = 1;
  adminError = '';

  constructor(private router: Router, private mochilaService: MochilaService) {}

  ngOnInit(): void {
    this.cargarMochila();
  }

  cargarMochila(): void {
    this.mochilaService.getMochila().subscribe({
      next: (data) => {
        this.mochila = data;
        this.cargando = false;
      },
      error: () => {
        this.error = 'Error al cargar la mochila';
        this.cargando = false;
      }
    });
  }

  get slots() {
    const items = [...this.mochila];
    while (items.length < 20) items.push(null as any);
    return items.slice(0, 20);
  }
  //función eliminar items 
  eliminarItem(id: number): void {
    this.mochilaService.eliminarItem(id).subscribe({
      next: () => this.cargarMochila(),
      error: () => this.error = 'Error al eliminar el item'
    });
  }

  adminAddXuxes(): void {
    this.adminError = '';
    if (this.mochila.length >= 20) {
      this.adminError = 'Mochila llena';
      return;
    }
    const userId = parseInt(localStorage.getItem('user_id') || '0');
    this.mochilaService.añadirItem(this.adminXuxeId, this.adminCantidad, userId).subscribe({
      next: () => this.cargarMochila(),
      error: (err) => this.adminError = err.error?.message || 'Error al añadir'
    });
  }

  navegarAlInicio()     { this.router.navigate(['/pagina-principal']); }
  navegarAlXuxedex()    { this.router.navigate(['/xuxedex']); }
  navegarAlInventario() { this.router.navigate(['/mochila']); }
  navegarAlAmigos()     { this.router.navigate(['/amigos']); }
  navegarAlBatalla()    { this.router.navigate(['/batalla']); }
  navegarAlChat()       { this.router.navigate(['/chat']); }
  navegarAlPerfil()     { this.router.navigate(['/info-usuario']); }
  navegarAlAdmin()      { this.router.navigate(['/admin']); }
}
