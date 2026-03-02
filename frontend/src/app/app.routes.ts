import { Routes } from '@angular/router';
import { RegisterComponent } from './register/register';
import { PaginaPrincipal } from './pagina-principal/pagina-principal';
import { LoginComponent } from './login/login';

export const routes: Routes = [
  { path: '', redirectTo: '/register', pathMatch: 'full' },
  
  { path: 'login',    component: LoginComponent },
  
  { path: 'register', component: RegisterComponent },

  // Ruta para la página principal
  { path: 'pagina-principal', component: PaginaPrincipal },

  // Cualquier otra → register

  { path: '**', redirectTo: 'register', pathMatch: 'full' }
];
