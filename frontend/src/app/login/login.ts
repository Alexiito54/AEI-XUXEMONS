import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../auth.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-login',
  standalone: true,                               
  imports: [CommonModule, ReactiveFormsModule],   
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
      user_id:  ['', Validators.required],
      password: ['', [Validators.required, Validators.minLength(6)]]
    });
  }

  get userIdControl() { return this.loginForm.get('user_id')!; }
  get passwordControl() { return this.loginForm.get('password')!; }

  onSubmit() {
    if (this.loginForm.invalid) return;

    this.loading = true;
    this.errorMsg = '';

    this.authService.login(this.loginForm.value).subscribe({
      next: (res: any) => {
        localStorage.setItem('token', res.token);
        localStorage.setItem('role', res.user.role);
        this.loading = false;
        this.router.navigate(['/home']);
      },
      error: () => {
        this.errorMsg = 'ID o contrasenya incorrectes';
        this.loading = false;
      }
    });
  }
}
