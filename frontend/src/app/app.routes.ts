import { Routes } from '@angular/router';
import { RegisterComponent } from './register/register';

export const routes: Routes = [
  { path: '', redirectTo: '/register', pathMatch: 'full' },
  
  { path: 'register', component: RegisterComponent },
  
  { path: '**', redirectTo: 'register', pathMatch: 'full' }
];
