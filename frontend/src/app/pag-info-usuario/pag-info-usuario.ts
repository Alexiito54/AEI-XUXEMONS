import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../auth.service';
import { Router } from '@angular/router';

interface UserData {
  id?: number;
  name: string;
  email: string;
  phone?: string;
  fecha_nacimiento?: string;
  ciudad?: string;
}

@Component({
  selector: 'app-pag-info-usuario',
  imports: [CommonModule, FormsModule],
  templateUrl: './pag-info-usuario.html',
  styleUrl: './pag-info-usuario.css',
})
export class PagInfoUsuario implements OnInit {
  // Estados de la UI
  isLoading = true;
  isEditing = false;
  isChangingPassword = false;
  errorMsg = '';
  successMsg = '';

  // Datos del usuario
  currentUser: UserData = {
    name: '',
    email: '',
    phone: '',
    fecha_nacimiento: '',
    ciudad: ''
  };

  userData: UserData = { ...this.currentUser };

  // Cambio de contraseña
  passwordForm = {
    currentPassword: '',
    newPassword: '',
    confirmPassword: ''
  };

  // Confirmación de eliminación
  showDeleteConfirm = false;
  deleteConfirmText = '';

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit() {
    this.loadUserData();
  }

  loadUserData() {
    this.isLoading = true;
    this.errorMsg = '';
    this.authService.getUser().subscribe({
      next: (response) => {
        this.currentUser = response.data || response;
        this.userData = { ...this.currentUser };
        this.isLoading = false;
      },
      error: (error) => {
        this.errorMsg = error.error?.message || 'Error al cargar los datos del usuario';
        this.isLoading = false;
        console.error('Error loading user:', error);
      }
    });
  }

  toggleEditMode() {
    if (this.isEditing) {
      this.userData = { ...this.currentUser };
    }
    this.isEditing = !this.isEditing;
    this.errorMsg = '';
    this.successMsg = '';
  }

  togglePasswordChange() {
    this.isChangingPassword = !this.isChangingPassword;
    this.resetPasswordForm();
    this.errorMsg = '';
    this.successMsg = '';
  }

  resetPasswordForm() {
    this.passwordForm = {
      currentPassword: '',
      newPassword: '',
      confirmPassword: ''
    };
  }

  saveUserData() {
    this.errorMsg = '';
    this.successMsg = '';

    // Validaciones
    if (!this.userData.name || this.userData.name.trim() === '') {
      this.errorMsg = '✕ El nombre es requerido';
      return;
    }

    if (!this.userData.email || this.userData.email.trim() === '') {
      this.errorMsg = '✕ El email es requerido';
      return;
    }

    if (!this.isValidEmail(this.userData.email)) {
      this.errorMsg = '✕ El email no es válido';
      return;
    }

    this.authService.updateUser(this.userData).subscribe({
      next: (response) => {
        this.currentUser = response.data || response;
        this.userData = { ...this.currentUser };
        this.isEditing = false;
        this.successMsg = 'Información actualizada correctamente';
        setTimeout(() => this.successMsg = '', 3000);
      },
      error: (error) => {
        this.errorMsg = error.error?.message || 'Error al actualizar los datos';
        console.error('Error updating user:', error);
      }
    });
  }

  savePasswordChange() {
    this.errorMsg = '';
    this.successMsg = '';

    // Validaciones
    if (!this.passwordForm.currentPassword) {
      this.errorMsg = '✕ La contraseña actual es requerida';
      return;
    }

    if (!this.passwordForm.newPassword || this.passwordForm.newPassword.length < 6) {
      this.errorMsg = '✕ La nueva contraseña debe tener al menos 6 caracteres';
      return;
    }

    if (this.passwordForm.newPassword !== this.passwordForm.confirmPassword) {
      this.errorMsg = '✕ Las contraseñas nuevas no coinciden';
      return;
    }

    this.authService.changePassword({
      current_password: this.passwordForm.currentPassword,
      new_password: this.passwordForm.newPassword
    }).subscribe({
      next: (response) => {
        this.resetPasswordForm();
        this.isChangingPassword = false;
        this.successMsg = 'Contraseña actualizada correctamente';
        setTimeout(() => this.successMsg = '', 3000);
      },
      error: (error) => {
        this.errorMsg = error.error?.message || 'Error al cambiar la contraseña';
        console.error('Error changing password:', error);
      }
    });
  }

  // Métodos para darse de baja
  startDeleteAccount() {
    this.showDeleteConfirm = true;
    this.deleteConfirmText = '';
    this.errorMsg = '';
  }

  cancelDelete() {
    this.showDeleteConfirm = false;
    this.deleteConfirmText = '';
  }

  confirmDeleteAccount() {
    if (this.deleteConfirmText !== 'ELIMINAR MI CUENTA') {
      this.errorMsg = '✕ Debes escribir "ELIMINAR MI CUENTA" para confirmar';
      return;
    }

    this.authService.deleteUser().subscribe({
      next: (response) => {
        this.successMsg = 'Cuenta eliminada correctamente. Redirigiendo...';
        setTimeout(() => {
          this.router.navigate(['/login']);
        }, 2000);
      },
      error: (error) => {
        this.errorMsg = error.error?.message || 'Error al eliminar la cuenta';
        console.error('Error deleting account:', error);
      }
    });
  }

  // Método auxiliar para validar email
  private isValidEmail(email: string): boolean {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  cancelEdit() {
    this.userData = { ...this.currentUser };
    this.isEditing = false;
    this.errorMsg = '';
  }
}
