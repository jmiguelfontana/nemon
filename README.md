# NEMON - Prueba Técnica de Indexado de Energía

Solución web completa compuesta por una **API REST en PHP 8.4.4 / Laravel** y un **Frontend SPA en Vue 3, TypeScript y Tailwind CSS** para gestionar y calcular precios indexados de energía según la especificación técnica de **NEMON**.

---

## 🏗️ Arquitectura del Sistema

```
nemon/
├── backend/            # API REST en PHP 8.4.4 + Laravel + Eloquent ORM + Swagger
│   ├── app/            # Controladores, Servicios, Modelos Eloquent y Form Requests
│   ├── database/       # Migraciones y Seeders con datos de prueba (Marzo 2025)
│   ├── tests/          # Tests automatizados (PHPUnit)
│   ├── AGENTS.md       # Especificaciones técnicas del Backend
│   └── README.md       # Guía de instalación y uso del Backend
├── frontend/           # SPA en Vue 3 + TypeScript + Vite + Tailwind CSS
│   ├── src/            # Componentes Vue 3, Tipos TS y Cliente API Axios
│   ├── AGENTS.md       # Especificaciones técnicas y guía estética del Frontend
│   └── README.md       # Guía de instalación y uso del Frontend
├── AGENTS.md           # Guía general de especificaciones del repositorio
└── README.md           # Presentación general de la prueba técnica
```

### 📂 Documentación de Módulos (Enlaces Directos)

- ⚙️ **Módulo Backend**: [backend/README.md](backend/README.md) | Especificaciones: [backend/AGENTS.md](backend/AGENTS.md)
- 🎨 **Módulo Frontend**: [frontend/README.md](frontend/README.md) | Especificaciones: [frontend/AGENTS.md](frontend/AGENTS.md)
- 📋 **Especificación General**: [AGENTS.md](AGENTS.md)

---

## 🐳 Inicio Rápido con Docker Compose (Recomendado)

Para desplegar la solución completa (Base de datos MySQL, API Backend Laravel y Frontend SPA Vue 3) en un solo comando:

```bash
docker compose up --build
```

- 🎨 **Frontend SPA**: `http://localhost:5173`
- ⚙️ **Backend API REST**: `http://localhost:8000`
- 📖 **Swagger UI**: `http://localhost:8000/api/documentation`

---

## 🔄 Integración y Despliegue Continuo (CI/CD con GitHub Actions)

El proyecto cuenta con un flujo automatizado de CI/CD configurado en [.github/workflows/ci-cd.yml](.github/workflows/ci-cd.yml):

- **Cada `git push` a `main`**:
  1. Ejecuta automáticamente los tests de Laravel en PHP 8.4 con MySQL.
  2. Compila y verifica la construcción de los contenedores Docker del Backend y Frontend.
  3. (Opcional) Actualiza el despliegue mediante SSH en servidor de producción.

---

## ⚡ Inicio Rápido Manual (Sin Docker)

### 1. Levantando el Backend (API REST)
*(Ver guía detallada en [backend/README.md](backend/README.md))*
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan l5-swagger:generate
php artisan serve
```
> La API REST quedará disponible en `http://localhost:8000`.  
> Documentación Swagger UI disponible en `http://localhost:8000/api/documentation`.

### 2. Levantando el Frontend (Vue 3 SPA)
*(Ver guía detallada en [frontend/README.md](frontend/README.md))*
```bash
cd frontend
npm install
npm run dev
```
> La interfaz web quedará disponible en `http://localhost:5173`.

---

## 📊 Especificación de Negocio y Fórmula

El cálculo del precio indexado se realiza evaluando la fórmula configurada por el usuario (ej. `([OMIE_MD] * 0.6) + 0.88`) para cada día y hora del periodo solicitado:

$$\text{precio\_indexado} = \frac{\sum (\text{eval}(\text{fórmula}) \times \text{consumo\_hora})}{\sum \text{consumo\_hora}}$$

- **Evaluación Segura**: La fórmula reemplaza el segmento `[OMIE_MD]` por el precio horario OMIE y se evalúa con un parser matemático seguro sin hacer uso de `eval()`.
- **Tratamiento Horario (`h1` - `h25`)**: Manejo adecuado de horas pico/valle y cambio de horario DST.
