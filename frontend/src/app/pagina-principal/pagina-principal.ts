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

  navegarAlInicio() {
    this.router.navigate(['/pagina-principal']);
  }

  navegarAlXuxedex() {
    this.router.navigate(['/xuxedex']);
  }

  navegarAlInventario() {
    this.router.navigate(['/mochila']);
  }

  navegarAlAmigos() {
    this.router.navigate(['/amigos']);
  }

  navegarAlBatalla() {
    this.router.navigate(['/batalla']);
  }

  navegarAlChat() {
    this.router.navigate(['/chat']);
  }

  navegarAlPerfil() {
    this.router.navigate(['/info-usuario']);
  }
  
  navegarAlAdmin() {
    this.router.navigate(['/admin']);
  }
}
