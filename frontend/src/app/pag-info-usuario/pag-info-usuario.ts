import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../auth.service';
import { Router } from '@angular/router';
import { HttpClient, HttpHeaders } from '@angular/common/http';

interface UserData {
  id?: number;
  name: string;
  apellidos?: string;
  email: string;
  phone?: string;
  fecha_nacimiento?: string;
  ciudad?: string;
  id_usuario?: string;
}

interface PerfilStats {
  total_xuxemons: number;
  total_batallas: number;
  total_amigos: number;
  nivel: number;
  xp_actual: number;
  xp_siguiente_nivel: number;
  porcentaje_victorias: number;
  xuxemons_por_tipo: { tipo: string; total: number; icono: string }[];
  preferencias: {
    notificaciones_batalla: boolean;
    perfil_publico: boolean;
    mensajes_privados: boolean;
  };
}

@Component({
  selector: 'app-pag-info-usuario',
  imports: [CommonModule, FormsModule],
  templateUrl: './pag-info-usuario.html',
  styleUrl: './pag-info-usuario.css',
  encapsulation: ViewEncapsulation.Emulated,
})

export class PagInfoUsuario implements OnInit {
  isLoading = true;
  isEditing = false;
  errorMsg = '';
  successMsg = '';

  currentUser: UserData = { name: '', email: '' };
  userData: UserData = { ...this.currentUser };

  stats: PerfilStats = {
    total_xuxemons: 0,
    total_batallas: 0,
    total_amigos: 0,
    nivel: 1,
    xp_actual: 0,
    xp_siguiente_nivel: 100,
    porcentaje_victorias: 0,
    xuxemons_por_tipo: [],
    preferencias: {
      notificaciones_batalla: false,
      perfil_publico: false,
      mensajes_privados: false
    }
  };

  showDeleteConfirm = false;
  deleteConfirmText = '';
  deletePassword = '';

  // Getters calculados
  get xpPorcentaje(): number {
    if (this.stats.xp_siguiente_nivel === 0) return 100;
    return Math.min(100, Math.round((this.stats.xp_actual / this.stats.xp_siguiente_nivel) * 100));
  }

  get rangoEntrenador(): string {
    const nivel = this.stats.nivel;
    if (nivel >= 50) return '★★★ MAESTRO';
    if (nivel >= 30) return '★★ ÉLITE';
    if (nivel >= 15) return '★ PRO';
    if (nivel >= 5)  return '◆ AVANZADO';
    return '◇ NOVATO';
  }

  get fechaRegistro(): string {
    // Formatea la fecha de registro del usuario
    return this.currentUser.fecha_nacimiento
      ? new Date(this.currentUser.fecha_nacimiento).toLocaleDateString('es-ES', { month: 'long', year: 'numeric' })
      : '—';
  }

  constructor(
    private authService: AuthService,
    private router: Router,
    private http: HttpClient
  ) {}

  ngOnInit() {
    this.loadUserData();
    this.loadPerfilStats();
  }

  loadUserData() {
  this.isLoading = true;
  this.authService.getUser().subscribe({
    next: (response: any) => {
      // El backend devuelve { user: {...} }
      this.currentUser = response.user || response;
      this.userData = { ...this.currentUser };
      this.isLoading = false;
    },
    error: (error: any) => {
      this.errorMsg = error.error?.message || 'Error al cargar los datos del usuario';
      this.isLoading = false;
    }
  });
}

loadPerfilStats() {
  const token = this.authService.getToken();
  const headers = new HttpHeaders({ Authorization: `Bearer ${token}` });

  this.http.get<any>('http://localhost:8000/api/perfil/stats', { headers }).subscribe({
    next: (response) => {
      this.stats = response;
    },
    error: () => {
      console.warn('Endpoint /perfil/stats no disponible');
    }
  });
}

  guardarPreferencia(key: keyof typeof this.stats.preferencias, valor: boolean) {
    const token = this.authService.getToken();
    const headers = new HttpHeaders({ Authorization: `Bearer ${token}` });
    this.http.post('http://localhost:8000/api/perfil/preferencias', { [key]: valor }, { headers })
      .subscribe({ error: () => console.warn('No se pudo guardar la preferencia') });
  }

  toggleEditMode() {
    if (this.isEditing) this.userData = { ...this.currentUser };
    this.isEditing = !this.isEditing;
    this.errorMsg = '';
    this.successMsg = '';
  }

  cancelEdit() {
    this.userData = { ...this.currentUser };
    this.isEditing = false;
    this.errorMsg = '';
  }

  saveUserData() {
  this.errorMsg = '';
  if (!this.userData.name?.trim()) { this.errorMsg = '✕ El nombre es requerido'; return; }
  if (!this.userData.email?.trim()) { this.errorMsg = '✕ El email es requerido'; return; }
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.userData.email)) { 
    this.errorMsg = '✕ Email no válido'; return; 
  }

  const payload = {
    name:      this.userData.name,
    apellidos: this.userData.apellidos || '',
    email:     this.userData.email,
  };

  this.authService.updateUser(payload).subscribe({
    next: (response: any) => {
      this.currentUser = response.user || response;
      this.userData = { ...this.currentUser };
      this.isLoading = false;
      // Actualizar localStorage
      localStorage.setItem('user', JSON.stringify(this.currentUser));
      this.isEditing = false;
      this.successMsg = 'Información actualizada correctamente';
      setTimeout(() => this.successMsg = '', 3000);
    },
    error: (error: any) => {
      this.errorMsg = error.error?.message || 'Error al actualizar los datos';
    }
  });
}

  startDeleteAccount() { this.showDeleteConfirm = true; this.deleteConfirmText = ''; this.deletePassword = ''; }
  cancelDelete() { this.showDeleteConfirm = false; }

  confirmDeleteAccount() {
    if (this.deleteConfirmText !== 'ELIMINAR MI CUENTA') {
      this.errorMsg = '✕ Debes escribir "ELIMINAR MI CUENTA"'; return;
    }
    if (!this.deletePassword?.trim()) {
      this.errorMsg = '✕ Debes ingresar tu contraseña'; return;
    }
    this.authService.deleteUser(this.deletePassword).subscribe({
      next: () => {
        this.authService.clearSession();
        this.router.navigate(['/login']);
      },
      error: (error: any) => { this.errorMsg = error.error?.message || 'Error al eliminar la cuenta'; }
    });
  }

  navegarAlInicio() { this.router.navigate(['/pagina-principal']); }
  navegarAlXuxedex() { this.router.navigate(['/xuxedex']); }
  navegarAlInventario() { this.router.navigate(['/mochila']); }
  navegarAlAmigos() { this.router.navigate(['/amigos']); }
  navegarAlBatalla() { this.router.navigate(['/batalla']); }
  navegarAlChat() { this.router.navigate(['/chat']); }
  navegarAlPerfil() { this.router.navigate(['/info-usuario']); }
  navegarAlAdmin() { this.router.navigate(['/admin']); }

  onLogout() {
    this.authService.logout().subscribe({
      next: () => { this.authService.clearSession(); this.router.navigate(['/login']); },
      error: () => { this.authService.clearSession(); this.router.navigate(['/login']); }
    });
  }
}