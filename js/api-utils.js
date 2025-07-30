// Configuracion global de la API
const API_BASE_URL = "http://localhost:8000/api";

// Funciones utilitarias para la API
class ApiClient {
    static getHeaders() {
        const token = localStorage.getItem("auth_token");
        return {
            "Content-Type": "application/json",
            "Accept": "application/json",
            ...(token && { "Authorization": `Bearer ${token}` })
        };
    }

    static async request(endpoint, options = {}) {
        const url = `${API_BASE_URL}${endpoint}`;
        const config = {
            headers: this.getHeaders(),
            ...options
        };

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }

            return data;
        } catch (error) {
            console.error("API Error:", error);
            throw error;
        }
    }

    static async get(endpoint) {
        return this.request(endpoint, { method: "GET" });
    }

    static async post(endpoint, body) {
        return this.request(endpoint, {
            method: "POST",
            body: JSON.stringify(body)
        });
    }

    static async put(endpoint, body) {
        return this.request(endpoint, {
            method: "PUT",
            body: JSON.stringify(body)
        });
    }

    static async delete(endpoint) {
        return this.request(endpoint, { method: "DELETE" });
    }
}

// Funciones de autenticacion
function getCurrentUser() {
    const userStr = localStorage.getItem("user");
    return userStr ? JSON.parse(userStr) : null;
}

function getAuthToken() {
    return localStorage.getItem("auth_token");
}

function isAuthenticated() {
    return !!(getAuthToken() && getCurrentUser());
}

function requireAuth() {
    if (!isAuthenticated()) {
        window.location.href = "login.html";
        return false;
    }
    return true;
}

function logout() {
    ApiClient.post("/logout").finally(() => {
        localStorage.removeItem("auth_token");
        localStorage.removeItem("user");
        window.location.href = "login.html";
    });
}

// Funcion para verificar roles
function hasRole(roleId) {
    const user = getCurrentUser();
    return user && user.rol_id === roleId;
}

function requireRole(roleId) {
    if (!hasRole(roleId)) {
        alert("No tienes permisos para acceder a esta funcion");
        return false;
    }
    return true;
}

// Mostrar mensajes
function showMessage(message, type = "success") {
    const alertDiv = document.createElement("div");
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.style.position = "fixed";
    alertDiv.style.top = "20px";
    alertDiv.style.right = "20px";
    alertDiv.style.zIndex = "9999";
    alertDiv.style.padding = "1rem";
    alertDiv.style.borderRadius = "8px";
    alertDiv.style.maxWidth = "300px";

    if (type === "error") {
        alertDiv.style.backgroundColor = "#f8d7da";
        alertDiv.style.color = "#721c24";
        alertDiv.style.border = "1px solid #f5c6cb";
    } else {
        alertDiv.style.backgroundColor = "#d4edda";
        alertDiv.style.color = "#155724";
        alertDiv.style.border = "1px solid #c3e6cb";
    }

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}

// Formatear fechas
function formatDate(dateString) {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    return date.toLocaleDateString("es-ES", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit"
    });
}

// Formatear dinero
function formatMoney(amount) {
    if (!amount) return "$0.00";
    return new Intl.NumberFormat("es-ES", {
        style: "currency",
        currency: "USD"
    }).format(amount);
}
