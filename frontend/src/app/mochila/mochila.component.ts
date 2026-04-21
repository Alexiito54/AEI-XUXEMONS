import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Item, MochilaService, MochilaItem } from '../services/mochila.service';

@Component({
  selector: 'app-mochila',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './mochila.component.html',
  styleUrls: ['./mochila.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class MochilaComponent implements OnInit {
  // Comprueba si el usuario actual es administrador
  // Se usa para mostrar o ocultar el panel de administración
  isAdmin = sessionStorage.getItem('role') === 'administrador';

  // ID interno del jugador guardado en sessionStorage
  // Es el identificador que usa el backend para el usuario conectado
  userInternalId = Number(sessionStorage.getItem('user_internal_id') || '0');

  // Lista de items que el administrador puede elegir para añadir
  availableItems: Item[] = [];

  // ID del item que el admin selecciona en el formulario
  adminItemId = 0;

  // Cantidad que quiere añadir el administrador
  adminCantidad = 1;

  // ID del jugador objetivo para añadir xuxes
  adminTargetUserId = this.userInternalId;

  // Mensajes del panel admin para errores y éxitos
  adminError = '';
  adminSuccess = '';

  // Datos de la mochila que se muestran en pantalla
  mochila: MochilaItem[] = [];

  // Controla si los datos aún están cargando
  cargando = true;

  // Mensaje de error si falla la carga de la mochila
  error = '';

  constructor(private router: Router, private mochilaService: MochilaService) {}

  ngOnInit(): void {
    // Se ejecuta al iniciar el componente
    // Carga los datos de la mochila desde el backend
    this.cargarMochila();

    // Solo para administradores cargamos la lista de items extra
    if (this.isAdmin) {
      this.cargarItems();
    }
  }

  cargarMochila(): void {
    // Llamada al servicio para obtener la mochila del usuario
    this.cargando = true;
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

  cargarItems(): void {
    // Carga los items que puede usar el admin para añadir xuxes
    this.mochilaService.getItems().subscribe({
      next: (items) => {
        this.availableItems = items.filter(item => item.tipo === 'xuxe');
        if (this.availableItems.length) {
          // Selecciona el primer item disponible por defecto
          this.adminItemId = this.availableItems[0].id;
        }
      },
      error: () => {
        this.adminError = 'Error al cargar la lista de xuxes';
      }
    });
  }

  // Crea una lista de 20 casillas para mostrar la mochila completa
  // Si hay menos de 20 items, rellena con casillas vacías
  get slots() {
    const items = [...this.mochila];
    while (items.length < 20) items.push(null as any);
    return items.slice(0, 20);
  }

  eliminarItem(id: number): void {
    // Elimina un item de la mochila y recarga la lista actualizada
    this.mochilaService.eliminarItem(id).subscribe({
      next: () => this.cargarMochila(),
      error: () => this.error = 'Error al eliminar el item'
    });
  }

  adminAddXuxes(): void {
    // Función que gestiona el formulario del admin para añadir xuxes
    this.adminError = '';
    this.adminSuccess = '';

    if (!this.adminTargetUserId || this.adminTargetUserId <= 0) {
      this.adminError = 'Introduce el ID interno del jugador objetivo';
      return;
    }

    if (!this.adminItemId) {
      this.adminError = 'Selecciona un item';
      return;
    }

    if (this.adminCantidad < 1) {
      this.adminError = 'La cantidad debe ser al menos 1';
      return;
    }

    this.mochilaService.añadirItem(this.adminItemId, this.adminCantidad, this.adminTargetUserId).subscribe({
      next: (res: any) => {
        this.adminSuccess = res.message || 'Xuxes añadidos correctamente';
        // Si el admin añade xuxes a su propia mochila, recargamos la pantalla
        if (this.adminTargetUserId === this.userInternalId) {
          this.cargarMochila();
        }
      },
      error: (err) => {
        this.adminError = err.error?.message || 'Error al añadir xuxes';
      }
    });
  }

  // Getters para calcular totales
  get totalXuxes(): number {
    return this.mochila.reduce((acc, item) => item.item.tipo === 'xuxe' ? acc + item.cantidad : acc, 0);
  }

  get totalVacunas(): number {
    return this.mochila.reduce((acc, item) => item.item.tipo === 'vacuna' ? acc + item.cantidad : acc, 0);
  }

  // Funciones para navegar a otras páginas de la aplicación
  navegarAlInicio()     { this.router.navigate(['/pagina-principal']); }
  navegarAlXuxedex()    { this.router.navigate(['/xuxedex']); }
  navegarAlInventario() { this.router.navigate(['/mochila']); }
  navegarAlAmigos()     { this.router.navigate(['/amigos']); }
  navegarAlBatalla()    { this.router.navigate(['/batalla']); }
  navegarAlChat()       { this.router.navigate(['/chat']); }
  navegarAlPerfil()     { this.router.navigate(['/info-usuario']); }
  navegarAlAdmin()      { this.router.navigate(['/admin']); }
}

