import { Component, ViewEncapsulation } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-pagina-principal',
  standalone: true,
  imports: [],
  templateUrl: './pagina-principal.html',
  styleUrls: ['./pagina-principal.css'],
   encapsulation: ViewEncapsulation.None  // <–– deja que :root afecte
})
export class PaginaPrincipal {
  constructor(private router: Router) {}

  navegarAlPerfil() {
    this.router.navigate(['/info-usuario']);
  }

}
