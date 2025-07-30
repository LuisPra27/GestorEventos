# Comandos a traves de la terminal que se encargaran del Backup de mi base de datos
# Crear backup manual
docker exec gestor_backup_ssl /bin/sh -c "/backup.sh"

# Verificar el backup generado
docker exec gestor_backup_ssl ls -lh /backups/daily

# Restaurar el backup en la base de datos
# Elimina la anterior
docker exec gestor_base_datos_ssl dropdb -U postgres eventos
# Crea la nueva
docker exec gestor_base_datos_ssl createdb -U postgres eventos
# Inserta los datos
docker exec gestor_backup_ssl gunzip -c /backups/daily/eventos-20250730.sql.gz | docker exec -i gestor_base_datos_ssl psql -U postgres -d eventos
###
###
###
###
# Dockerfile.frontend
# Certificado SSL
# Instalar OpenSSL para generar certificados
RUN apk add --no-cache openssl

# Crear directorios para SSL
RUN mkdir -p /etc/ssl/certs /etc/ssl/private

# Generar certificado SSL autofirmado
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
# La llave y la ubicacion donde se guarda
    -keyout /etc/ssl/private/nginx-selfsigned.key \
# El certificado publico y la ubicacion donde se guarda
    -out /etc/ssl/certs/nginx-selfsigned.crt \
# La información interna del certificado
    -subj "/C=EC/ST=Manabi/L=Manta/O=GestorEventos/OU=IT/CN=localhost/emailAddress=admin@gestoreventos.ec"

# Crear archivo DH para mayor seguridad
RUN openssl dhparam -out /etc/ssl/certs/dhparam.pem 2048