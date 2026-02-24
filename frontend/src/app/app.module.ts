import { bootstrapApplication } from '@angular/platform-browser';
import { importProvidersFrom } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { ReactiveFormsModule } from '@angular/forms';
import { provideRouter } from '@angular/router';

import { App } from './app';
import { routes } from './app.routes';

bootstrapApplication(App, {
  providers: [
    importProvidersFrom([HttpClientModule, ReactiveFormsModule]),
    provideRouter(routes)
  ]
}).catch(err => console.error(err));
