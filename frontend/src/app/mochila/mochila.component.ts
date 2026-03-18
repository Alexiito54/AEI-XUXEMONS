import { Component, ViewEncapsulation } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-mochila',
  standalone: true,
  imports: [],
  templateUrl: './mochila.component.html',
  styleUrls: ['./mochila.component.css'],
     encapsulation: ViewEncapsulation.None  // <–– deja que :root afecte
})
export class MochilaComponent {
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
