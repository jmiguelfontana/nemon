# Backend API REST - Cálculo de Precio Indexado de Energía (NEMON)

API REST desarrollada en **PHP 8.4.4** y **Laravel** encargada de la lógica de negocio para el cálculo del precio indexado de energía a partir de consumos y precios horarios del mercado mayorista OMIE_MD.

---

## 🌍 Entorno de Producción

- **Documentación API (Swagger)**: [https://nemon.robbytherobot.tech/api/documentation](https://nemon.robbytherobot.tech/api/documentation)
- **Ruta Base API**: `https://nemon.robbytherobot.tech/api/`

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
   DB_USERNAME=nemon_app
   DB_PASSWORD=nemon_secret
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

## 🛠️ Pruebas Manuales (Requisito 3.3)

Si deseas evaluar la aplicación introduciendo datos manualmente en la base de datos (mediante sentencias `INSERT`):
1. Levanta el entorno local (pasos anteriores).
2. Conéctate a tu base de datos local `nemon_energy` (ej. vía DBeaver o phpMyAdmin en `localhost:3306`).
3. Hemos dejado un archivo de cortesía llamado `pruebas_manuales_3.3.sql` en la raíz del proyecto. Puedes copiar y pegar esos comandos SQL para inyectar datos reales del 15 de Marzo de 2025.
4. Entra en la web local y calcula el precio indexado para ese día para comprobar el resultado.

---

## 🧪 Ejecución de Pruebas Automatizadas (PHPUnit / Pest)

Para verificar el correcto funcionamiento de los cálculos, casos de validación y códigos HTTP (`200`, `400`, `404`, `500`):

```bash
php artisan test
```

> **NOTA:** El sistema incluye un **Feature Test** clave (`CalculationTest.php`) que inyecta datos de consumo y precios totalmente controlados en la base de datos de pruebas (`nemon_energy_test`) para certificar que el motor matemático devuelve exactamente los valores financieros y ponderaciones esperadas.

---

## 🔒 Seguridad (Prevención RCE)

Para la evaluación dinámica de fórmulas matemáticas suministradas desde el frontend, **se ha prohibido el uso de la función `eval()`** nativa de PHP. Todo el análisis y resolución de las fórmulas se delega en la librería especializada de análisis léxico **`nxp/math-executor`**, mitigando por completo el riesgo de inyecciones de código (Remote Code Execution).

---

## 📑 Endpoints Principales

- `POST /api/calculate`: Calcula el precio indexado en función del rango de fechas y la fórmula configurada.
- `GET /api/consumptions`: Lista los registros de consumo por horas (`h1`-`h25`).
- `GET /api/prices`: Lista los precios del mercado OMIE_MD por horas (`h1`-`h25`).
