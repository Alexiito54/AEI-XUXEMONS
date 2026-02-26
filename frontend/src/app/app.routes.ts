import { Routes } from '@angular/router';
import { RegisterComponent } from './register/register';

export const routes: Routes = [
  // Ruta principal '/' → redirige a register
  { path: '', redirectTo: '/register', pathMatch: 'full' },
  
  // Ruta register
  { path: 'register', component: RegisterComponent },
  
  // Cualquier otra → register
  { path: '**', redirectTo: 'register', pathMatch: 'full' }
];
