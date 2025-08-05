# Railway Dockerfile para Frontend
FROM nginx:alpine

# Instalar curl para health checks
RUN apk add --no-cache curl

# Crear directorios necesarios
RUN mkdir -p /var/log/nginx /usr/share/nginx/html

# Copiar archivos del proyecto
COPY *.html /usr/share/nginx/html/
COPY css/ /usr/share/nginx/html/css/
COPY js/ /usr/share/nginx/html/js/
COPY images/ /usr/share/nginx/html/images/

# Copiar configuración de Nginx para Railway
COPY nginx-railway.conf /etc/nginx/conf.d/default.conf

# Crear páginas de error
RUN echo '<!DOCTYPE html><html><head><title>404 - Página no encontrada</title><meta charset="utf-8"><style>body{font-family:Arial,sans-serif;text-align:center;margin-top:50px}</style></head><body><h1>404 - Página no encontrada</h1><p>La página que buscas no existe.</p><a href="/">Volver al inicio</a></body></html>' > /usr/share/nginx/html/404.html

RUN echo '<!DOCTYPE html><html><head><title>Error del servidor</title><meta charset="utf-8"><style>body{font-family:Arial,sans-serif;text-align:center;margin-top:50px}</style></head><body><h1>Error del servidor</h1><p>El servidor encontró un error temporal.</p><a href="/">Volver al inicio</a></body></html>' > /usr/share/nginx/html/50x.html

# Configurar permisos
RUN chown -R nginx:nginx /usr/share/nginx/html \
    && chmod -R 755 /usr/share/nginx/html

# Exponer puerto para Railway
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Comando de inicio
CMD ["nginx", "-g", "daemon off;"]
