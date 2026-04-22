import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../auth.service';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, HttpClientModule],
  templateUrl: './login.html',
  styleUrls: ['./login.css']
})
export class LoginComponent {

  loginForm: FormGroup;
  loading: boolean = false;
  errorMsg: string = '';

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router
  ) {
    this.loginForm = this.fb.group({
      id_usuario:  ['', Validators.required],
      password: ['', [Validators.required, Validators.minLength(6)]]
    });
  }

  get idUsuarioControl() { return this.loginForm.get('id_usuario')!; }
  get passwordControl() { return this.loginForm.get('password')!; }

  onSubmit() {
    if (this.loginForm.invalid) return;

    this.loading = true;
    this.errorMsg = '';
    console.log('Enviando login con:', this.loginForm.value);

    // Clear any old tokens
    localStorage.removeItem('token');
    sessionStorage.removeItem('token');

    this.authService.login(this.loginForm.value).subscribe({
      next: (res: any) => {
        console.log('Login response:', res);
        console.log('Token:', res.token);
        console.log('User:', res.user);

        localStorage.setItem('token', res.token);
        localStorage.setItem('role', res.user.rol);
        localStorage.setItem('user_id', res.user.id_usuario);
        localStorage.setItem('user_internal_id', res.user.id.toString());

        this.loading = false;
        console.log('Navegando a pagina-principal');
        this.router.navigate(['/pagina-principal']);
      },
      error: (err) => {
        console.error('Login error:', err);
        this.errorMsg = 'ID o contraseña incorrectos';
        this.loading = false;
      }
    });
  }
}
