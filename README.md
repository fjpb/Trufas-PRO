# 🍄 TrufaControl Pro

Sistema web profesional para la gestión de producción de trufa negra.  
Permite administrar melgas (líneas de cultivo), árboles individuales y cosechas con control detallado por ejemplar.

---

## 🚀 Características principales

- 🔐 **Autenticación segura** con sesiones PHP y contraseñas hasheadas.
- 🌳 **Gestión de melgas**: crear, editar, eliminar y visualizar resumen de árboles productores.
- 🌲 **Gestión de árboles**:
  - Ver árboles por melga con estado de producción, fechas clave y estado sanitario.
  - Edición rápida de campos individuales (`onchange`).
  - Añadir múltiples árboles nuevos con códigos secuenciales.
  - Eliminar selección múltiple de árboles (con eliminación en cascada de cosechas).
- 🍄 **Registro de cosechas**:
  - Fecha, número de trufas, peso (kg), calidad y observaciones.
  - Marca automáticamente el árbol como "productor" en su primera cosecha.
- 📊 **Historial completo**:
  - Global con filtros por melga y rango de fechas.
  - Por árbol con gráfico de barras (kg vs. cantidad de trufas) usando Chart.js.
- ✏️ **Edición y eliminación** de cosechas.
- 📎 **Exportación de datos** a CSV y respaldo en JSON.
- 🔄 **Actualización dinámica** de datos sin recargar página.
- ⏱️ **Mantenimiento de sesión** (ping cada 10 minutos).
- 📱 **Diseño responsivo** adaptable a móviles y tablets.

---

## 🛠️ Tecnologías utilizadas

| Capa          | Tecnologías |
|---------------|-------------|
| **Frontend**  | HTML5, CSS3, JavaScript (ES6), Chart.js |
| **Backend**   | PHP 7.4+, MySQL, Sesiones |
| **Servidor**  | Apache / InfinityFree (compatible) |
| **Otros**     | Fetch API, CORS, JSON |

---

## 📂 Estructura del proyecto
/
├── index.php # Interfaz principal
├── config.php # Configuración de BD, sesiones y CORS
├── api/
│ ├── login.php # Autenticación
│ ├── logout.php # Cierre de sesión
│ ├── melgas.php # CRUD de melgas
│ ├── arboles.php # CRUD de árboles (individual y múltiple)
│ ├── datos.php # CRUD de cosechas
│ ├── estadisticas.php # Totales para el dashboard
│ └── exportar.php # Exportación CSV
└── README.md # Este archivo

---

## 🗄️ Base de datos

Adjunto sql

⚙️ Instalación y configuración
1. Requisitos del servidor
PHP ≥ 7.4

MySQL

Servidor Apache (recomendado)

Extensión mysqli habilitada

2. Pasos de instalación
Clona o descarga el repositorio en la carpeta htdocs o public_html.

Crea una base de datos en tu hosting y ejecuta el script SQL anterior.

Configura config.php:

Cambia las constantes DB_HOST, DB_USER, DB_PASS, DB_NAME con los datos de tu BD.

Modifica el dominio CORS $allowed_origin (ej. https://tudominio.com).

Sube los archivos al servidor (puedes usar FTP o el administrador de archivos del hosting).

Ajusta permisos (recomendado 644 para archivos, 755 para carpetas).

Accede a https://tudominio.com/index.php e inicia sesión (cambiar claves y usuarios en DB).

🤝 Contribuciones
Las contribuciones son bienvenidas. Por favor, abre un issue o envía un pull request con tus mejoras.

📄 Licencia
Este proyecto está bajo la licencia MIT.
Consulta el archivo LICENSE para más detalles.

✉️ Contacto
Desarrollado por FJPB 
Repositorio: https://github.com/fjpb/Trufas-PRO

