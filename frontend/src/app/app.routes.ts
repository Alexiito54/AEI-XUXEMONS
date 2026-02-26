import { Routes } from '@angular/router';
import { RegisterComponent } from './register/register';
import { PaginaPrincipal } from './pagina-principal/pagina-principal';

export const routes: Routes = [
  // Ruta principal '/' → redirige a register
  { path: '', redirectTo: '/register', pathMatch: 'full' },
  
  // Ruta register
  { path: 'register', component: RegisterComponent },
  
  // Ruta para la página principal
  { path: 'pagina-principal', component: PaginaPrincipal },

  // Cualquier otra → register
  { path: '**', redirectTo: 'register', pathMatch: 'full' }
];
