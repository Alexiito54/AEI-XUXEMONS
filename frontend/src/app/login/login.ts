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
    this.authService.login(this.loginForm.value).subscribe({
      next: (res: any) => {

        localStorage.setItem('token', res.token);
        localStorage.setItem('user', JSON.stringify(res.user));
        sessionStorage.setItem('role', res.user.rol);
        sessionStorage.setItem('user_id', res.user.id_usuario);
        sessionStorage.setItem('user_internal_id', res.user.id.toString());

        this.loading = false;
        this.router.navigate(['/pagina-principal']);
      },
      error: (err: any) => {
        console.error('Login error:', err);
        this.errorMsg = err.error?.message || 'ID o contraseña incorrectos';
        this.loading = false;
      }
    });
  }
}
