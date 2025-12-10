document.addEventListener('DOMContentLoaded',function(){
// obtener el aprametro id de la Url
const paramer = new URLSearchParams(window.location.search);
const id = paramer.get('id');

// contnedor donde  va a aprerecer el articulo
const contenedor =document.getElementById('contenido-articulo');

// Base de datos simple de artículos 
    const articulos = {
        automatizacion: {
            titulo: "La Automatización en la Industria Moderna",
            fecha: "10 de noviembre de 2025",
            imagen: "imagenes/automation.jpg",
            contenido: `
                La automatización permite optimizar procesos, reducir costos y aumentar la calidad en la producción.
                Gracias a la integración de sensores, controladores y algoritmos de inteligencia artificial, las empresas alcanzan una mayor eficiencia operativa.
                En este artículo exploramos cómo la industria 4.0 está transformando la manera en que concebimos el trabajo y la producción.
            `
        },
        tecnologia: {
            titulo: "Avances en Inteligencia Artificial",
            fecha: "8 de noviembre de 2025",
            imagen: "imagenes/IA.jpg",
            contenido: `
                <p>La inteligencia artificial está transformando todos los sectores, desde la salud hasta la educación.</p>
                <p>Los algoritmos de aprendizaje automático permiten analizar grandes volúmenes de datos para mejorar la toma de decisiones.</p>
            `
        },
        robotica: {
            titulo: "El Futuro de la Robótica",
            fecha: "5 de noviembre de 2025",
            contenido: `
                <p>La robótica moderna combina mecánica, electrónica e informática para crear máquinas que asisten al ser humano.</p>
                <p>Los robots colaborativos son un ejemplo claro de cómo la tecnología puede integrarse con el trabajo humano.</p>
            `
        },
         neumatica: {
            titulo: "sistemas neumáticos",
            fecha: "5 de noviembre de 2025",
            imagen:"imagenes/neumatic.jpg",
            contenido: `
                <p>La neumatica moderna combina mecánica, electrónica e informática para crear máquinas que asisten al ser humano.</p>
                <p>Los neumatica colaborativos son un ejemplo claro de cómo la tecnología puede integrarse con el trabajo humano.</p>
            `
        },
        mantenimiento: {
            titulo: "Mantenimiento Mecanico",
            fecha: "5 de noviembre de 2025",
            imagen: "imagenes/maintanace.webp",
            contenido: `
                <p>El mantenimiento mecánico es la disciplina esencial enfocada en preservar la integridad
                 y funcionalidad de los equipos industriales y la maquinaria. Esta área se clasifica en 
                 tres estrategias principales: el Mantenimiento Correctivo, que responde a fallas 
                 inesperadas; el Mantenimiento Preventivo, que se basa en la planificación de tareas
                  rutinarias como la lubricación y la inspección por tiempo o uso; y el Mantenimiento 
                  Predictivo, que utiliza tecnologías como el análisis de vibraciones y la termografía
                   para monitorear la condición real del equipo, permitiendo anticipar el momento óptimo
                    de la reparación y maximizar la vida útil del componente.</p>



               <p> Las tareas fundamentales dentro de esta disciplina buscan mitigar el desgaste, siendo la
                 lubricación industrial crítica para reducir la fricción y el calor. Además, el
                  alineamiento de ejes y el balanceo de componentes rotativos son cruciales para
                   controlar la vibración, que es la principal causa de fallas en rodamientos, sellos 
                   y estructuras. Adoptar una estrategia de mantenimiento avanzada, como la predictiva, 
                   transforma el enfoque de la reacción a la proactividad, garantizando la fiabilidad 
                   operativa y la seguridad en el entorno industrial. humano.</p>
            `
        }
    };

   /* mostrar articulo correspondiente 
   if (id && articulos[id]){

   const articulo = articulos[id];
        contenedor.innerHTML = `
            <div class="article-header">
              <h1 class="fw-bold">${articulo.titulo}</h1>
              <p class="text-muted">${articulo.fecha}</p>
            </div>
            <img src="${articulo.imagen}" alt="${articulo.titulo}" class="article-img img-fluid shadow-sm">
            <div class="article-content">${articulo.contenido}</div>
            <a href="blog.html" class="volver">← Volver al Blog</a>
        `;
   }
   else{
    contenedor.innerHTML = `
            <h1>Artículo no encontrado</h1>
            <p>El artículo solicitado no existe o fue eliminado.</p>
            <a href="blog.html" class="volver">← Volver al Blog</a>
        `;
    
   }*/
     const articulo = articulos[id];
     if (articulo) {
      document.getElementById("tituloArticulo").textContent = articulo.titulo;
      document.getElementById("imagenArticulo").src = articulo.imagen;
      document.getElementById("contenidoArticulo").innerHTML = articulo.contenido;
     
    } else {
      document.getElementById("tituloArticulo").textContent = "Artículo no encontrado";
      document.getElementById("contenidoArticulo").textContent = "Lo sentimos, el artículo que buscas no existe.";
      document.getElementById("imagenArticulo").style.display = "none";
    }

});