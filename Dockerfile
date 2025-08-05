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

# Crear configuración optimizada para Railway
RUN echo 'server { \
    listen 80; \
    server_name _; \
    root /usr/share/nginx/html; \
    index index.html index.htm; \
    \
    # Configuración para Railway \
    location / { \
        try_files $uri $uri/ /index.html; \
    } \
    \
    # Configuración para API proxy \
    location /api/ { \
        proxy_pass http://backend:8000/api/; \
        proxy_http_version 1.1; \
        proxy_set_header Upgrade $http_upgrade; \
        proxy_set_header Connection "upgrade"; \
        proxy_set_header Host $host; \
        proxy_set_header X-Real-IP $remote_addr; \
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for; \
        proxy_set_header X-Forwarded-Proto $scheme; \
        proxy_cache_bypass $http_upgrade; \
    } \
    \
    # Configuración de archivos estáticos \
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ { \
        expires 1y; \
        add_header Cache-Control "public, immutable"; \
    } \
    \
    # Health check endpoint \
    location /health { \
        access_log off; \
        return 200 "healthy\n"; \
        add_header Content-Type text/plain; \
    } \
    \
    # Configuración de errores \
    error_page 404 /404.html; \
    error_page 500 502 503 504 /50x.html; \
}' > /etc/nginx/conf.d/default.conf

# Eliminar configuración por defecto de nginx
RUN rm -f /etc/nginx/nginx.conf

# Crear nueva configuración principal
RUN echo 'user nginx; \
worker_processes auto; \
error_log /var/log/nginx/error.log notice; \
pid /var/run/nginx.pid; \
\
events { \
    worker_connections 1024; \
    use epoll; \
    multi_accept on; \
} \
\
http { \
    include /etc/nginx/mime.types; \
    default_type application/octet-stream; \
    \
    log_format main "$remote_addr - $remote_user [$time_local] \"$request\" " \
                    "$status $body_bytes_sent \"$http_referer\" " \
                    "\"$http_user_agent\" \"$http_x_forwarded_for\""; \
    \
    access_log /var/log/nginx/access.log main; \
    \
    # Optimizaciones para Railway \
    sendfile on; \
    tcp_nopush on; \
    tcp_nodelay on; \
    keepalive_timeout 65; \
    types_hash_max_size 2048; \
    client_max_body_size 10M; \
    \
    # Compresión \
    gzip on; \
    gzip_vary on; \
    gzip_min_length 1024; \
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json; \
    \
    include /etc/nginx/conf.d/*.conf; \
}' > /etc/nginx/nginx.conf

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
