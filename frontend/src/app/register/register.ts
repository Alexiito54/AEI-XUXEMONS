import { Component } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators, AbstractControl } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { AuthService } from '../auth.service';
import { HttpClientModule } from '@angular/common/http';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, HttpClientModule],
  templateUrl: './register.html',
  styleUrls: ['./register.css']  
})
export class RegisterComponent {
  registerForm: FormGroup;
  loading = false;
  confirmTouched = false;

  constructor(private fb: FormBuilder, private authService: AuthService) {
    this.registerForm = this.fb.group({
      name: ['', [Validators.required, Validators.maxLength(50)]],
      surname: ['', [Validators.required, Validators.maxLength(100)]],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
      password_confirmation: ['', [Validators.required]]
    }, { validators: this.passwordMatchValidator });
  }

  get nameControl() { return this.registerForm.get('name')!; }
  get surnameControl() { return this.registerForm.get('surname')!; }
  get emailControl() { return this.registerForm.get('email')!; }
  get passwordControl() { return this.registerForm.get('password')!; }
  get passwordMismatch() { 
    return this.registerForm.hasError('mismatch') && this.confirmTouched; 
  }

  passwordMatchValidator = (g: AbstractControl): { [key: string]: any } | null => {
    const pass = g.get('password')?.value;
    const confirm = g.get('password_confirmation')?.value;
    return pass === confirm ? null : { mismatch: true };
  };

  onPasswordConfirmTouched() {
    this.confirmTouched = true;
  }

  onSubmit() {
    if (this.registerForm.valid) {
      this.loading = true;
      this.authService.register(this.registerForm.value).subscribe({
        next: (response: any) => {
      alert('Registrado! ID: ' + response.user_id);
      this.loading = false;
      this.registerForm.reset();
        },
        error: (err) => {
          console.error(err);
          alert('Error: ' + (err.error?.error || 'Registro falló'));
          this.loading = false;
        }
      });
    }
  }
}
