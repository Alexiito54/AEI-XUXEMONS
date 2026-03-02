import { Routes } from '@angular/router';
import { RegisterComponent } from './register/register';
import { LoginComponent } from './login/login';

export const routes: Routes = [
  { path: '', redirectTo: '/register', pathMatch: 'full' },
  
  { path: 'login',    component: LoginComponent },
  
  { path: 'register', component: RegisterComponent },
  
  { path: '**', redirectTo: 'register', pathMatch: 'full' }
];
