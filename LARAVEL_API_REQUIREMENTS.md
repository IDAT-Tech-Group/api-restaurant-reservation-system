# Laravel API Requirements (Actualizado)

Este documento contiene la estructura formal y exhaustiva de todos los **endpoints (rutas)** y **estructuras JSON** que tu backend en Laravel deberá proveer. Está alineado con la última versión de tu Frontend en Vite, el cual incorpora gestión de zonas, mapas interactivos de mesas, clientes, platos y reservas avanzadas.

> [!NOTE]
> Las URLs propuestas asumen que el prefijo en Laravel es `/api/`. Todos los endpoints de mantenimiento (Crear, Actualizar, Eliminar) orientados a administración deberían estar protegidos por el middleware `auth:sanctum` y validar roles de Administrador.

---

## 🔐 1. Autenticación y Seguridad (Auth)
*(Rutas para inicio y cierre de sesión de administradores y clientes comunes).*

### 1.1 Iniciar Sesión (Login)
- **POST** `/api/login`
- **Body a recibir:** `username` (correo), `password`.
- **Respuesta (Status 200 OK):**
  ```json
  {
    "success": true,
    "user": { "id": 1, "username": "admin@gmail.com", "name": "Administrador", "role": "admin" },
    "token": "1|uYtGh...jksda"
  }
  ```

### 1.2 Registrarse (Register)
- **POST** `/api/register`
- **Body a recibir:** `name`, `username` (correo), `password`, `phone`.
- **Respuesta:** Similar al login directo. Creado en BD y autenticado.

### 1.3 Cerrar Sesión (Logout)
- **POST** `/api/logout` (Requerirá enviar el token Bearer en el Header).

---

## 🪑 2. Mesas y Zonas (Infraestructura)
*(Alimento para el InteractiveTableMap y la sección de Zonas)*

### 2.1 Zonas del Restaurante
*Ej. General, Interior, Terraza, Barra.*
- **GET** `/api/zonas` : Listar todas las zonas.
- **POST** `/api/zonas` : Crear nueva zona (`name`, `icon`).
- **PUT** `/api/zonas/{id}` : Editar nombre o ícono.
- **DELETE** `/api/zonas/{id}` : Eliminar zona.
- **Estructura GET:**
  ```json
  [ { "id": "salon", "name": "Salón Principal", "icon": "🛋️" } ]
  ```

### 2.2 Mesas (Mantenimiento)
*Cada mesa está atada a una Zona y tiene una capacidad máxima.*
- **GET** `/api/mesas` : Listar todas las mesas con sus relaciones de zonas.
- **POST** `/api/mesas` : Crear mesa.
  - **Body esperado:** `number` (identificador ej. "10"), `zone_id` (ej. "salon"), `capacity` (ej. 4).
- **PUT** `/api/mesas/{id}` : Editar mesa (cambiar capacidad o zona).
- **DELETE** `/api/mesas/{id}` : Dar de baja una mesa.
- **Estructura GET:**
  ```json
  [ { "id": "1", "zone_id": "salon", "number": 1, "capacity": 4, "is_active": true } ]
  ```

---

## ⏰ 3. Horarios y Turnos (Mantenimiento)
- **GET** `/api/horarios` : Listar franjas horarias habilitadas para reservas.
- **POST** `/api/horarios` : Registrar nuevo bloque horario.
  - **Body esperado:** `start_time` (ej. "19:00"), `duration_mins` (ej. 120 para 2h).
- **PUT** `/api/horarios/{id}` : Editar hora / duración.
- **DELETE** `/api/horarios/{id}` : Borrar turno.

---

## 🍽️ 4. Platos y Menú (Carta Comercial)
- **GET** `/api/platos` : Devuelve toda la carta para pintarse en el `Landing.jsx`.
- **POST** `/api/platos` : Registrar plato nuevo (`name`, `description`, `price`, `emoji`, `category`).
- **PUT** `/api/platos/{id}` : Actualiza descripción o precio.
- **DELETE** `/api/platos/{id}` : Eliminar de carta.
- **Estructura GET:**
  ```json
  [ { "id": 1, "name": "Tagliatelle al Ragù", "price": 18, "emoji": "🍝" } ]
  ```

---

## 👥 5. Clientes / Usuarios
*(Aquellos que se registran para reservar o se generan desde una reserva pública)*
- **GET** `/api/clientes` : Listar todos los clientes o perfiles base (para el Dashboard del Administrador).
- **PUT/PATCH** `/api/clientes/{id}/perfil` : Actualizar datos del cliente (teléfono, nombre).

---

## 📅 6. Reservaciones (El Núcleo Operativo)

### 6.1 Listar Reservaciones
- **GET** `/api/reservas` 
  - *(Recomendación: Soporte de filtros `?date=2023-10-10`, `?status=reservado`, `?user_id=X`)*. Esto evitará que en el futuro la app colapse cargando años de historial.
- **Estructura Respuesta:**
  ```json
  [
    {
      "id": 101,
      "user_id": 15, // Si está atado a cuenta (opcional)
      "name": "Juan Perez",
      "email": "juan@example.com",
      "phone": "+1 555 123",
      "date": "2024-05-20",
      "start_time": "19:00",
      "end_time": "21:00",
      "persons": 2,
      "zone_id": "terraza",
      "table_id": "1", // El ID de la mesa ocupada
      "status": "pendiente", // pendiente, reservado, completado, cancelado
      "notes": "Aniversario"
    }
  ]
  ```

### 6.2 Crear Reserva (Cruce de Horarios)
Cualquier cliente logueado o admin puede originar esto. **AQUÍ Laravel DEBE hacer la validación de que la `table_id` enviada NO esté ya asignada en esa `date` y en un rango de `start_time` y `end_time` cursante.**
- **POST** `/api/reservas`
- **Body esperado:** Misma estructura de arriba sin la parte del `id`.

### 6.3 Actualizar Status de Reserva (Pagos)
El frontend actualmente tiene un botón *"💳 Simular Pago (50%)"* que cambia la reserva.
- **PATCH** `/api/reservas/{id}/status`
- **Body a recibir:** `{ "status": "reservado" }`

### 6.4 Eliminar o Cancelar
- **DELETE** `/api/reservas/{id}`

---

## 🔥 Recomendaciones Finales de Arquitectura BBDD
Al comparar estos requerimientos con tu base de datos antigua (el script SQL de `idatrestaurant`), se notaron discrepancias clave que debes corregir en tus migraciones de Laravel para que todo funcione a la perfección:

1. **Usuarios (Administradores y Seguridad)**
   - El SQL antiguo carecía de la tabla de usuarios.
   - **Crea:** `users(id, name, email, password, phone, role)` para el manejo de sesiones (Login/Register) mediante Tokens (Sanctum o JWT).
2. **Platos (Precios y Textos Comerciales)**
   - El SQL antiguo omitió el `precio` y la `descripcion` de los platos. 
   - **Crea/Edita:** `dishes(id, name, description, price (decimal), emoji, status)`. (Si no añades un precio o descripción comercial, la interfaz React no podrá mostrar la carta apropiadamente ni calcular pagos).
3. **Mesas y Zonas**
   - El SQL almacenaba "ubicacionmesa" como texto. Lo correcto para esta app es separar ambas cosas y ligarlas por ForeignKey.
   - **Crea:** `zones(id, name, icon)`.
   - **Crea:** `tables(id, zone_id (FK), number, capacity, is_active)`. *Recordatorio:* El campo `capacity` (antes `cantidadsillas`) es fundamental porque React lo lee para ocultar las mesas automáticamentee si un cliente quiere reservar para 10 y la mesa es de 2.
4. **Horarios Dinámicos**
   - El SQL antiguo ataba la `horainicio` suelta en la tabla reserva.
   - **Crea:** `time_slots(id, start_time, duration)`. Para que el restaurante mantenga control de franjas horarias estrictas y no dependamos de textos tipeados.
5. **Reservaciones (Cruce de Datos)**
   - **Crea:** `reservations(id, user_id (FK opcional), table_id (FK), date, start_time, end_time, persons, status, tipopago, notes)`.
   - *El mayor desafío de Laravel aquí será crear el validador en el Endpoint POST:* Antes de grabar la reserva en DB, el backend deberá verificar que, para esa `date`, entre ese margen de horas (`start_time` y `end_time`), la `table_id` elegida no esté ocupada.
