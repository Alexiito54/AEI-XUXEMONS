import { HttpInterceptorFn } from '@angular/common/http';

export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const token = localStorage.getItem('token');
  console.log('INTERCEPTOR ejecutado, token:', token);
  
  const publicEndpoints = ['/api/register', '/api/login'];
  const isPublic = publicEndpoints.some(endpoint => 
    req.url.includes(endpoint)
  );
  
  if (token && !isPublic) {
    req = req.clone({
      setHeaders: { Authorization: `Bearer ${token}` }
    });
  }
  
  return next(req);
};
