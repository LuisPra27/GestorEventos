#!/bin/bash

echo "🔒 Configurando Gestor de Eventos con SSL..."

# Detener contenedores actuales si existen
echo "📦 Deteniendo contenedores existentes..."
docker-compose down 2>/dev/null || true

# Construir y levantar con SSL
echo "🏗️ Construyendo contenedores con SSL..."
docker-compose -f docker-compose-ssl.yml up --build -d

echo "⏳ Esperando que los servicios se inicializen..."
sleep 30

# Verificar estado
echo "🔍 Verificando estado de los contenedores..."
docker-compose -f docker-compose-ssl.yml ps

echo ""
echo "✅ ¡Gestor de Eventos con SSL está listo!"
echo ""
echo "🌐 Accesos disponibles:"
echo "   HTTP:  http://localhost (redirige a HTTPS)"
echo "   HTTPS: https://localhost (SSL habilitado)"
echo "   API:   https://localhost/api/"
echo "   Estado: https://localhost/status"
echo ""
echo "⚠️  NOTA: El certificado es autofirmado, acepta la advertencia de seguridad del navegador"
echo ""
echo "📋 Comandos útiles:"
echo "   Ver logs: docker-compose -f docker-compose-ssl.yml logs -f"
echo "   Detener:  docker-compose -f docker-compose-ssl.yml down"
echo "   Estado:   docker-compose -f docker-compose-ssl.yml ps"
