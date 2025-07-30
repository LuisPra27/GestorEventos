let menu = document.querySelector('#menu-bars');
let navbar = document.querySelector('.navbar');

menu.onclick = () =>{
  menu.classList.toggle('fa-times');
  navbar.classList.toggle('active');
}

let themeToggler = document.querySelector('.theme-toggler');
let toggleBtn = document.querySelector('.toggle-btn');

toggleBtn.onclick = () =>{
  themeToggler.classList.toggle('active');
}

window.onscroll = () =>{
  menu.classList.remove('fa-times');
  navbar.classList.remove('active');
  themeToggler.classList.remove('active');
}

document.querySelectorAll('.theme-toggler .theme-btn').forEach(btn =>{
  
  btn.onclick = () =>{
    let color = btn.style.background;
    document.querySelector(':root').style.setProperty('--main-color', color);
  }

});

var swiper = new Swiper(".home-slider", {
  effect: "coverflow",
  grabCursor: true,
  centeredSlides: true,
  slidesPerView: "auto",
  coverflowEffect: {
    rotate: 0,
    stretch: 0,
    depth: 100,
    modifier: 2,
    slideShadows: true,
  },
  loop:true,
  autoplay:{
    delay: 3000,
    disableOnInteraction:false,
  }
});

var swiper = new Swiper(".review-slider", {
    slidesPerView: 1,
    grabCursor: true,
    loop:true,
    spaceBetween: 10,
    breakpoints: {
      0: {
          slidesPerView: 1,
      },
      700: {
        slidesPerView: 2,
      },
      1050: {
        slidesPerView: 3,
      },    
    },
    autoplay:{
      delay: 5000,
      disableOnInteraction:false,
  }
});

// Cargar servicios dinamicamente desde la API
document.addEventListener('DOMContentLoaded', function() {
    loadServices();
});

async function loadServices() {
    const priceContainer = document.getElementById('priceContainer');
    
    try {
        // Usar fetch directo ya que no necesitamos autenticacion para ver servicios publicos
        const response = await fetch('http://localhost:8000/api/services');
        const data = await response.json();
        
        if (data.success && data.data.length > 0) {
            // Filtrar solo servicios activos
            const activeServices = data.data.filter(service => service.activo);
            
            if (activeServices.length === 0) {
                priceContainer.innerHTML = `
                    <div class="no-services-message">
                        <i class="fas fa-info-circle"></i>
                        <p>No hay servicios disponibles en este momento.</p>
                    </div>
                `;
                return;
            }
            
            // Generar HTML para cada servicio
            let servicesHTML = '';
            activeServices.forEach(service => {
                servicesHTML += `
                    <div class="box">
                        <h3>${service.nombre}</h3>
                        <div class="price">$${parseFloat(service.precio).toFixed(2)} <span>/ ${service.duracion_horas}hrs</span></div>
                        <div class="list">
                            <p><i class="fas fa-info-circle"></i> ${service.descripcion || 'Servicio profesional de alta calidad'}</p>
                            <p><i class="fas fa-check"></i> Duracion: ${service.duracion_horas} horas</p>
                            <p><i class="fas fa-check"></i> Servicio profesional</p>
                            <p><i class="fas fa-check"></i> Atencion personalizada</p>
                            <p><i class="fas fa-check"></i> Garantia de calidad</p>
                        </div>
                        <a href="register.html" class="btn">elegir plan</a>
                    </div>
                `;
            });
            
            priceContainer.innerHTML = servicesHTML;
        } else {
            priceContainer.innerHTML = `
                <div class="no-services-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>No hay servicios disponibles en este momento.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading services:', error);
        priceContainer.innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <p>Error al cargar los servicios. Por favor, intenta mas tarde.</p>
            </div>
        `;
    }
}

// Funcion para refrescar los servicios (puede ser llamada externamente)
function refreshServices() {
    loadServices();
}

// Hacer la funcion disponible globalmente
window.refreshServices = refreshServices;