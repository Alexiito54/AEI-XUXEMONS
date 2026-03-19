import { Routes } from '@angular/router';
import { authGuard } from './guards/auth.guard';
import { adminGuard } from './guards/admin.guard';

export const routes: Routes = [
  // Rutas públicas
  { path: 'login',    loadComponent: () => import('./login/login').then(m => m.LoginComponent) },
  { path: 'register', loadComponent: () => import('./register/register').then(m => m.RegisterComponent) },

  // Rutas protegidas (login requerido)
  {
    path: 'home',
    canActivate: [authGuard],
    loadComponent: () => import('./pagina-principal/pagina-principal').then(m => m.PaginaPrincipal)
  },
  {
    path: 'mochila',
    canActivate: [authGuard],
    loadComponent: () => import('./mochila/mochila.component').then(m => m.MochilaComponent)
  },

  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: '**', redirectTo: 'login' }
];
