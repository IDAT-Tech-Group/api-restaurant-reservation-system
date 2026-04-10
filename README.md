# 🍽️ API Restaurante (Backend Laravel)

Este repositorio contiene el código fuente del **Backend** del sistema de reserva de mesas, construido en el framework **Laravel 9**. Aquí se centraliza la seguridad, la gestión de bases de datos, las validaciones estrictas y toda la lógica de negocio para que el restaurante opere sin problemas.

---

## 📁 1. Arquitectura y Ubicación de Archivos Clave

Si necesitas saber o explicar dónde está construida cada pieza del sistema, este es el mapa detallado:

*   **🌐 Rutas (`routes/api.php`)**
    *   *¿Qué hace?* Es el "conmutador telefónico" de la API. Define todas las URLs a las que el Frontend puede llamar (`/api/platos`, `/api/login`, `/api/reservas`), y les dice qué Controlador debe encargarse de dar la respuesta.
    *   *Detalle técnico:* Agrupa endpoints públicos e implementa el *middleware* `auth:sanctum` combinado con un middleware propio para proteger los endpoints restringidos únicamente a usuarios **Administradores**.

*   **🎮 Controladores (`app/Http/Controllers/...`)**
    *   *¿Qué hacen?* Contienen la "inteligencia" y funcionalidad real. Reciben la petición del Frontend, le preguntan a la Base de Datos a través de los Modelos y devuelven el `.json` correspondiente. Destacan:
        *   `AuthController.php`: Registro, Inicio de Sesión y desconexión. Genera los tokens *Sanctum*.
        *   `ReservationController.php`: El núcleo del negocio. Comprueba si una mesa tiene la capacidad pedida y **verifica rigurosamente que no haya un cruce de horarios (Overbooking)** antes de insertar una reserva en la base de datos.
        *   `DishController.php`, `ZoneController.php`, `...`: Operaciones rutinarias CRUD (Crear, Leer, Actualizar, Eliminar).

*   **📦 Modelos Eloquent (`app/Models/...`)**
    *   *¿Qué hacen?* Son el puente directo con cada tabla de tu base de datos MySQL. En su interior declaran explícitamente sus relaciones (*Ej. "Una Mesa pertenece a una Zona", "Una Mesa tiene múltiples Reservas"*).
    *   *Ubicación:* `User.php`, `Dish.php`, `Zone.php`, `Table.php`, `TimeSlot.php`, `Reservation.php`.

*   **💾 Migraciones (`database/migrations/...`)**
    *   *¿Qué hacen?* Son planos arquitectónicos que Laravel usa para crear la Base de Datos de cero en cualquier máquina con el comando `php artisan migrate`. Contiene qué campos, qué tipos de datos exactos y qué llaves foráneas lleva cada tabla.

*   **🔐 Medio de Seguridad (`app/Http/Middleware/AdminMiddleware.php`)**
    *   *¿Qué hace?* Un muro de protección interceptor construido a mano. Atrapa las peticiones de los Controllers que lo invoquen y revisa en crudo que el usuario que intenta hacer la acción tenga el `role` exacto de `admin`.

---

## 📡 2. Resumen de Endpoints Disponibles

Todas las URLs deben ir precedidas por `http://127.0.0.1:8000/api`.

### 🟢 Acceso Público (Cualquiera puede acceder)
*   **POST** `/login` -> Valida usuario/contraseña y devuelve un **Bearer Token**.
*   **POST** `/register` -> Registra una nueva cuenta de Cliente.
*   **GET** `/platos`, `/zonas`, `/mesas`, `/horarios` -> Devuelve la data pre-existente para la carga pública (ej. menú y landing interactivo).
*   **POST** `/reservas` -> Crea tu reserva, analizando colisiones y el número de personas.

### 🛡️ Acceso Restringido (Requieren Token Sanctum / Privilegios de Admin)
*(Se debe mandar la cabecera `Authorization: Bearer <TUTOKEN>`)*

*   **Administración Base:** `POST`, `PUT`, `DELETE` en:
    *   `/platos/{id}` (Actualización del menú interactivo)
    *   `/zonas/{id}` (Administrar los pisos/niveles del restaurante)
    *   `/mesas/{id}` (Dar de alta, ampliar mesas o darlas de baja)
    *   `/horarios/{id}` (Modificar franjas autorizadas para comer)
*   **Manejo de Clientes y Cobros:**
    *   `PATCH /reservas/{id}/status` -> Cambia el flujo interno de una reserva (Ej. simulación de PAGO del Frontend del 50%).
    *   `GET /clientes` y `PUT /clientes/{id}/perfil`

---

## ⚙️ 3. Puesta en Marcha (Resumen Técnico)

1.  En la terminal, asegúrate de tener una Base de Datos MySQL llamada `apirestaurante` lista en tu XAMPP (por puerto 3306).
2.  Ejecutar en la raíz del proyecto para crear las tablas desde los planos (Migraciones):
    ```bash
    php artisan migrate
    ```
    *(Si deseas insertar los datos de prueba, puedes importar los ficheros provistos en la carpeta `/database` del proyecto dentro de XAMPP).*
3.  Despertar la aplicación:
    ```bash
    php artisan serve
    ```
4.  Consumir la data `http://127.0.0.1:8000/api` interconectándose con React y Fetch nativo (Configuraciones detalladas en el `FRONTEND_INTEGRATION_GUIDE.md`).
