import { Routes } from '@angular/router';
import { RegisterComponent } from './register/register';
import { PaginaPrincipal } from './pagina-principal/pagina-principal';
import { LoginComponent } from './login/login';
import { PagInfoUsuario } from './pag-info-usuario/pag-info-usuario';
import { MochilaComponent } from './mochila/mochila.component';
import { authGuard } from './guards/auth.guard'; // ← añade esto

export const routes: Routes = [
  { path: '', redirectTo: '/register', pathMatch: 'full' },
  
  { path: 'login',    component: LoginComponent },
  { path: 'register', component: RegisterComponent },

  // Rutas protegidas 
  { path: 'pagina-principal', component: PaginaPrincipal, canActivate: [authGuard] },
  { path: 'info-usuario',     component: PagInfoUsuario,  canActivate: [authGuard] },
  { path: 'mochila',          component: MochilaComponent, canActivate: [authGuard] },

  { path: '**', redirectTo: 'register', pathMatch: 'full' }
];
