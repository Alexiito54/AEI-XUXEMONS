import { Component } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-pag-info-usuario',
  imports: [],
  templateUrl: './pag-info-usuario.html',
  styleUrl: './pag-info-usuario.css',
})
export class PagInfoUsuario {
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
