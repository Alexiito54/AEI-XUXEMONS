import { Routes } from '@angular/router';
import { RegisterComponent } from './register/register';
import { PagInfoUsuario } from './pag-info-usuario/pag-info-usuario';

export const routes: Routes = [
  // Ruta principal '/' → redirige a register
  { path: '', redirectTo: '/register', pathMatch: 'full' },
  
  // Ruta register
  { path: 'register', component: RegisterComponent },

 // Ruta info-usuario
  { path: 'info-usuario', component: PagInfoUsuario },

  // Cualquier otra → register
  { path: '**', redirectTo: 'register', pathMatch: 'full' }
];
