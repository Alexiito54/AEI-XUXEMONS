import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Item, MochilaService, MochilaItem } from '../services/mochila.service';

interface SlotView {
  id: number;
  item: {
    id: number;
    nombre: string;
    icono: string;
    apilable: boolean;
    tipo: string;
  };
  cantidad: number;
}

const MAX_STACK = 5;
const TOTAL_SLOTS = 20;

@Component({
  selector: 'app-mochila',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './mochila.component.html',
  styleUrls: ['./mochila.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class MochilaComponent implements OnInit {

  isAdmin = sessionStorage.getItem('role') === 'administrador';
  userInternalId = Number(sessionStorage.getItem('user_internal_id') || '0');

  availableItems: Item[] = [];
  availableVacunas: Item[] = [];

  adminItemId = 0;
  adminVacunaId = 0;
  adminCantidad = 1;
  adminTargetUserId = this.userInternalId;
  adminError = '';
  adminSuccess = '';

  mochila: MochilaItem[] = [];
  cargando = true;
  error = '';

  constructor(private router: Router, private mochilaService: MochilaService) {}

  ngOnInit(): void {
    this.cargarMochila();
    if (this.isAdmin) {
      this.cargarItems();
    }
  }

  cargarMochila(): void {
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
    this.mochilaService.getItems().subscribe({
      next: (items) => {
        this.availableItems = items.filter(item => item.tipo === 'xuxe');
        this.availableVacunas = items.filter(item => item.tipo === 'vacuna');

        if (this.availableItems.length) {
          this.adminItemId = this.availableItems[0].id;
        }
        if (this.availableVacunas.length) {
          this.adminVacunaId = this.availableVacunas[0].id;
        }
      },
      error: () => {
        this.adminError = 'Error al cargar la lista de items';
      }
    });
  }

  get slots(): (SlotView | null)[] {
    const result: (SlotView | null)[] = [];

    for (const mochilaItem of this.mochila) {
      if (mochilaItem.item.apilable) {
        let remaining = mochilaItem.cantidad;
        while (remaining > 0) {
          const stackSize = Math.min(remaining, MAX_STACK);
          result.push({
            id: mochilaItem.id,
            item: mochilaItem.item,
            cantidad: stackSize
          });
          remaining -= stackSize;
        }
      } else {
        for (let i = 0; i < mochilaItem.cantidad; i++) {
          result.push({
            id: mochilaItem.id,
            item: mochilaItem.item,
            cantidad: 1
          });
        }
      }
    }

    while (result.length < TOTAL_SLOTS) result.push(null);
    return result.slice(0, TOTAL_SLOTS);
  }

  get slotsUsados(): number {
    return this.slots.filter(s => s !== null).length;
  }

  get isFull(): boolean {
    return this.slotsUsados >= TOTAL_SLOTS;
  }

  get totalXuxes(): number {
    return this.mochila
      .filter(item => item.item.tipo === 'xuxe')
      .reduce((acc, item) => acc + item.cantidad, 0);
  }

  get totalVacunas(): number {
    return this.mochila
      .filter(item => item.item.tipo === 'vacuna')
      .reduce((acc, item) => acc + item.cantidad, 0);
  }

  asSlot(slot: SlotView | null): SlotView {
    return slot as SlotView;
  }

  eliminarItem(id: number): void {
    this.mochilaService.eliminarItem(id).subscribe({
      next: () => this.cargarMochila(),
      error: () => this.error = 'Error al eliminar el item'
    });
  }

  adminAddXuxes(): void {
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
        if (this.adminTargetUserId === this.userInternalId) {
          this.cargarMochila();
        }
      },
      error: (err) => {
        this.adminError = err.error?.message || 'Error al añadir xuxes';
      }
    });
  }

  adminAddVacuna(): void {
    this.adminError = '';
    this.adminSuccess = '';

    if (!this.adminTargetUserId || this.adminTargetUserId <= 0) {
      this.adminError = 'Introduce el ID interno del jugador objetivo';
      return;
    }
    if (!this.adminVacunaId) {
      this.adminError = 'Selecciona una vacuna';
      return;
    }

    this.mochilaService.añadirItem(this.adminVacunaId, 1, this.adminTargetUserId).subscribe({
      next: (res: any) => {
        this.adminSuccess = res.message || 'Vacuna añadida correctamente';
        if (this.adminTargetUserId === this.userInternalId) {
          this.cargarMochila();
        }
      },
      error: (err) => {
        this.adminError = err.error?.message || 'Error al añadir vacuna';
      }
    });
  }

  navegarAlInicio()     { this.router.navigate(['/pagina-principal']); }
  navegarAlXuxedex()    { this.router.navigate(['/xuxedex']); }
  navegarAlInventario() { this.router.navigate(['/mochila']); }
  navegarAlPerfil()     { this.router.navigate(['/info-usuario']); }
}
