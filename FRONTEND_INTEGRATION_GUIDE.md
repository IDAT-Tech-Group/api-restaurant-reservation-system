# 🚀 Guía de Integración Backend a Frontend (Consideraciones para el Frontend existente)

Esta guía documenta los considerandos principales a la hora de consumir la API de Laravel desde la lógica o las funciones `fetch` que ya existen construidas actualmente en el Frontend.

## 1. Peticiones de Validación y JSON Obligatorio

Todas sus llamadas hacia la API de Laravel necesitan declararse explícitamente como clientes JSON para evitar redirecciones indeseadas. 
Al usar tu función actual de Fetch en el Frontend, asegúrate de que sus cabeceras base estén inyectando:

```json
{
    "Accept": "application/json",
    "Content-Type": "application/json"
}
```

> **¿Por qué `Accept: application/json` es obligatorio?** Si su función base de Fetch llega a omitirlo, ante cualquier error (401, 422), Laravel intentará forzar una redirección tradicional en vez de devolver el objeto de error, provocando un falso "CORS Error" o fallos de red en React.

## 2. Autenticación, Login y Token (Sanctum) 🔐

El Endpoint para el inicio de sesión (`POST /login`) devolverá dentro de su objeto JSON, un nodo llamado de la siguiente forma:

```javascript
// Luego de que tu función fetch llame exitosamente a /login
// La respuesta json traera este objeto
const respuesta = await funcionFetchEnElFront('/login', data);

const elToken = respuesta.token;
const informacionUsuario = respuesta.user;
```
A partir de este punto, todas tus funciones Fetch que vayan dirigidas a endpoints protegidos (Ej. Guardar mesa, Cambiar horarios o Pagar la Reserva) deberán adjuntar esta cabecera autorizando el uso de la API:
`"Authorization": "Bearer " + elToken`

## 3. Manejo Crítico para el Overbooking (El estatus 422)

En Laravel me aseguré de construir el validador de **Cruce de Horarios** exigido en sus requerimientos de negocio. Si desde el Front se intenta llamar a la creación de una reserva y esta se solapa con una mesa ocupada, Laravel va a retornar un **422 Unprocessable Entity**.

Asegúrate de que en el Frontend controlen y dibujen amistosamente ese tipo de error al usuario.
Al atrapar un `422`, el body del JSON vendrá así:
```json
{
    "message": "La mesa no está disponible en este horario u ocurre otro error de validación."
}
```

## 4. Obtener Recursos Abiertos 🍽️

La gran ventaja es que tu página de Landing, Carta, Horarios y Zonas, no necesitan de lógica extra más que tu función de Fetch apuntando a los endpoints correctos como siempre.

- Para la carta: `GET /api/platos`
- Para las mesas (trae las zonas integradas): `GET /api/mesas`
- Para las franjas horarias base: `GET /api/horarios`

## 5. El Tema del "CORS" (Importante) 🚨

Laravel 9+ configurado en este lado del BACKEND ya viene preparado de forma nativa para soportar CORS en todas las rutas en `/api/`.
Por defecto aceptará las peticiones del frontend sin queja. Sin embargo, si al integrarse les salta un error absoluto de origen de CORS desde el navegador, la regla se flexibilizará yendo dentro de Laravel a `config/cors.php`, y en `allowed_origins` agregando la URI particular donde el Frontend despierte (ej. `http://localhost:5173`).
