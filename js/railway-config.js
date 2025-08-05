// Configuración para Railway - Frontend
// Este archivo maneja la configuración automática del entorno

window.RAILWAY_CONFIG = {
    // Configuración del backend
    getBackendUrl: function() {
        const hostname = window.location.hostname;
        
        // En desarrollo local
        if (hostname === 'localhost' || hostname === '127.0.0.1') {
            return 'http://localhost:8000/api';
        }
        
        // En Railway
        if (hostname.includes('railway.app')) {
            // Método 1: URL específica del backend (recomendado)
            // Cambiar esta URL por la real del backend después del despliegue
            const backendUrl = prompt(
                'Ingrese la URL del backend de Railway:', 
                'https://gestor-eventos-backend-production.railway.app/api'
            );
            if (backendUrl && backendUrl.trim()) {
                localStorage.setItem('backend_url', backendUrl.trim());
                return backendUrl.trim();
            }
            
            // Método 2: URL guardada previamente
            const savedUrl = localStorage.getItem('backend_url');
            if (savedUrl) {
                return savedUrl;
            }
            
            // Método 3: Intentar adivinar basado en el nombre del frontend
            const parts = hostname.split('.');
            if (parts.length > 0) {
                const serviceName = parts[0].replace('frontend', 'backend').replace('gestor-eventos', 'gestor-eventos-backend');
                return `https://${serviceName}.railway.app/api`;
            }
        }
        
        // Fallback
        return `${window.location.protocol}//${hostname}:8000/api`;
    },
    
    // Configuración de timeouts
    REQUEST_TIMEOUT: 15000,
    
    // Headers predeterminados
    DEFAULT_HEADERS: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    },
    
    // Configuración de CORS
    CORS_ENABLED: true,
    
    // Debug mode
    DEBUG: window.location.hostname.includes('localhost')
};

// Función para testear la conexión al backend
window.testBackendConnection = async function() {
    const url = window.RAILWAY_CONFIG.getBackendUrl() + '/health';
    
    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: window.RAILWAY_CONFIG.DEFAULT_HEADERS
        });
        
        if (response.ok) {
            const data = await response.json();
            console.log('✅ Backend conectado correctamente:', data);
            return true;
        } else {
            console.error('❌ Error en el backend:', response.status);
            return false;
        }
    } catch (error) {
        console.error('❌ No se pudo conectar al backend:', error);
        return false;
    }
};

// Auto-test en desarrollo
if (window.RAILWAY_CONFIG.DEBUG) {
    window.addEventListener('load', () => {
        setTimeout(() => {
            window.testBackendConnection();
        }, 1000);
    });
}

console.log('🚀 Railway Config cargada:', window.RAILWAY_CONFIG.getBackendUrl());
