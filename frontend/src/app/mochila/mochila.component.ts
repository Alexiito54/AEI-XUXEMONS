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

  navegarAlPerfil() {
    this.router.navigate(['/info-usuario']);
  }

  navegarAlInventario() {
    this.router.navigate(['/mochila']);
  }
}
