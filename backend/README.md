# Backend API REST - Cálculo de Precio Indexado de Energía (NEMON)

API REST desarrollada en **PHP 8.4.4** y **Laravel** encargada de la lógica de negocio para el cálculo del precio indexado de energía a partir de consumos y precios horarios del mercado mayorista OMIE_MD.

---

## 🚀 Requisitos del Sistema

- **PHP**: `^8.4.4` (con extensiones `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `json`)
- **Base de Datos**: MySQL 8.0+ / MariaDB 10.4+
- **Gestor de Paquetes**: Composer 2.x

---

## 🛠️ Instalación y Configuración

1. **Clonar e ingresar al directorio backend**:
   ```bash
   cd backend
   ```

2. **Instalar dependencias**:
   ```bash
   composer install
   ```

3. **Configurar el archivo de entorno (`.env`)**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Asegúrate de ajustar los datos de conexión a MySQL en `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nemon_energy
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Ejecutar migraciones y datos de prueba (Seeders)**:
   ```bash
   php artisan migrate --seed
   ```

5. **Generar documentación interactiva Swagger / OpenAPI**:
   ```bash
   php artisan l5-swagger:generate
   ```

6. **Iniciar el servidor de desarrollo**:
   ```bash
   php artisan serve
   ```
   La API estará escuchando en `http://localhost:8000`.

---

## 📖 Documentación Swagger UI

Una vez iniciado el servidor, la documentación interactiva de los endpoints está disponible en:
👉 `http://localhost:8000/api/documentation`

---

## 🧪 Ejecución de Pruebas Automatizadas (PHPUnit / Pest)

Para verificar el correcto funcionamiento de los cálculos, casos de validación y códigos HTTP (`200`, `400`, `404`, `500`):

```bash
php artisan test
```

---

## 📑 Endpoints Principales

- `POST /api/calculate`: Calcula el precio indexado en función del rango de fechas y la fórmula configurada.
- `GET /api/consumptions`: Lista los registros de consumo por horas (`h1`-`h25`).
- `GET /api/prices`: Lista los precios del mercado OMIE_MD por horas (`h1`-`h25`).
