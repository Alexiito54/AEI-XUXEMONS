import { Routes } from '@angular/router';
import { RegisterComponent } from './register/register';
import { PaginaPrincipal } from './pagina-principal/pagina-principal';
import { LoginComponent } from './login/login.component';

export const routes: Routes = [
  { path: '', redirectTo: '/register', pathMatch: 'full' },
  
  { path: 'register', component: RegisterComponent },

  // Ruta para la página principal
  { path: 'pagina-principal', component: PaginaPrincipal },

  {path: 'login',component: LoginComponent},

  // Cualquier otra → register

  { path: '**', redirectTo: 'register', pathMatch: 'full' }
];
