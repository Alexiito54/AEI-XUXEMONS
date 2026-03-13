import { Routes } from '@angular/router';
import { RegisterComponent } from './register/register';
import { PaginaPrincipal } from './pagina-principal/pagina-principal';
import { LoginComponent } from './login/login';
import { PagInfoUsuario } from './pag-info-usuario/pag-info-usuario';
import { MochilaComponent } from './mochila/mochila.component';

export const routes: Routes = [
  { path: '', redirectTo: '/register', pathMatch: 'full' },
  
  { path: 'login',    component: LoginComponent },
  
  { path: 'register', component: RegisterComponent },

  // Ruta para la página principal
  { path: 'pagina-principal', component: PaginaPrincipal },

 // Ruta info-usuario
  { path: 'info-usuario', component: PagInfoUsuario },

  { path: 'mochila', component: MochilaComponent },

  // Cualquier otra → register

  { path: '**', redirectTo: 'register', pathMatch: 'full' }
];
