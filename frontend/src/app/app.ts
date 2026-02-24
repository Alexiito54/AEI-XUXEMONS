import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterOutlet } from '@angular/router';
@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule, RouterOutlet],
  template: `
    <div class="app">
      <h1> XUXEMONS DAW</h1>
      <router-outlet />
    </div>
  `,
  styles: [`
    .app { padding: 20px; text-align: center; }
    h1 { color: #ff6b35; }
  `]
})
export class AppComponent {
  title = 'xuxemons-frontend';
}
