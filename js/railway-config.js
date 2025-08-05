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
        
        // En Railway - URL específica (CAMBIAR DESPUÉS DEL DESPLIEGUE)
        if (hostname.includes('railway.app')) {
            // Configurar la URL real del backend aquí
            const backendUrl = localStorage.getItem('backend_url');
            if (backendUrl) {
                return backendUrl;
            }
            
            // URL por defecto - CAMBIAR por la real del backend
            return 'https://gestoreventos-backend-production.railway.app/api';
        }
        
        // Fallback
        return `${window.location.protocol}//${hostname}:8000/api`;
    },
    
    // Función para configurar manualmente la URL del backend
    setBackendUrl: function(url) {
        if (url && url.trim()) {
            localStorage.setItem('backend_url', url.trim());
            console.log('✅ Backend URL configurada:', url.trim());
            return true;
        }
        return false;
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

// Función para configurar la URL del backend manualmente
window.setBackendUrl = function(url) {
    return window.RAILWAY_CONFIG.setBackendUrl(url);
};

// Función para testear la conexión al backend
window.testBackendConnection = async function() {
    const url = window.RAILWAY_CONFIG.getBackendUrl() + '/ping';
    
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

console.log('🚀 Railway Config cargada. Backend URL:', window.RAILWAY_CONFIG.getBackendUrl());
console.log('💡 Para configurar manualmente: setBackendUrl("https://tu-backend.railway.app/api")');

// Auto-test en desarrollo
if (window.RAILWAY_CONFIG.DEBUG) {
    window.addEventListener('load', () => {
        setTimeout(() => {
            window.testBackendConnection();
        }, 1000);
    });
}
