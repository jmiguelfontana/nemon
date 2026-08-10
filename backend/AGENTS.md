# ESPECIFICACIONES TÉCNICAS DEL BACKEND (NEMON - PRECIO INDEXADO)

Este documento define las reglas de desarrollo, arquitectura y estándares de código estrictos para el backend de la aplicación.

---

## ⚙️ 1. ENTORNO Y TECNOLOGÍAS

- **PHP Version**: `PHP 8.4.4` (utilizando tipado estricto `declare(strict_types=1);` y características modernas de PHP 8.4).
- **Framework**: `Laravel` (Última versión estable).
- **ORM / Acceso a Datos**: **Uso estricto y exclusivo de Eloquent ORM** (`App\Models\Consumption` y `App\Models\Price`). Queda prohibido el uso de consultas SQL crudas o la fachada `DB::table()` directa.
- **Documentación de API**: **Swagger / OpenAPI 3.0** (mediante `darkaonline/l5-swagger` con Atributos/Anotaciones PHP 8).

---

## 🗄️ 2. ESTRUCTURA DE BASE DE DATOS (MySQL)

### 2.1 Tabla `consumptions`
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `date`: `DATE UNIQUE NOT NULL`
- `h1` a `h25`: `DOUBLE NULLABLE` (consumo de energía en kWh por cada hora del día)
- `created_at` / `updated_at`: `TIMESTAMP`

### 2.2 Tabla `prices`
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `date`: `DATE UNIQUE NOT NULL`
- `h1` a `h25`: `DOUBLE NULLABLE` (precio de energía en €/kWh para el segmento OMIE_MD)
- `created_at` / `updated_at`: `TIMESTAMP`

---

## 📦 3. MODELOS ELOQUENT (`App\Models`)

### 3.1 Modelo `Consumption`
- `$fillable`: `['date', 'h1', 'h2', ..., 'h25']`
- `$casts`: `['date' => 'date', 'h1' => 'float', ..., 'h25' => 'float']`
- Scope Eloquent: `scopeBetweenDates($query, string $startDate, string $endDate)`

### 3.2 Modelo `Price`
- `$fillable`: `['date', 'h1', 'h2', ..., 'h25']`
- `$casts`: `['date' => 'date', 'h1' => 'float', ..., 'h25' => 'float']`
- Scope Eloquent: `scopeBetweenDates($query, string $startDate, string $endDate)`

---

## 📖 4. DOCUMENTACIÓN SWAGGER / OPENAPI

- Toda acción/controlador de la API REST **debe incluir atributos Swagger / OpenAPI** (`#[OA\Post]`, `#[OA\Get]`, `#[OA\RequestBody]`, `#[OA\Response]`, `#[OA\JsonContent]`).
- La documentación interactiva debe estar accesible en la URL `/api/documentation`.
- **Regla Obligatoria**: Con cada nuevo endpoint o modificación de payload/respuesta, se debe ejecutar `php artisan l5-swagger:generate` y verificar la especificación.

---

## 🌐 5. API REST & ENDPOINTS

### Endpoint Principal: `POST /api/calculate`

#### Payload de Entrada (JSON):
```json
{
  "start_date": "2025-03-01",
  "end_date": "2025-03-31",
  "formula": "([OMIE_MD] * 0.6) + 0.88"
}
```

#### Respuestas y Códigos HTTP Obligatorios en Swagger & API:

- **`200 OK`**: Cálculo realizado con éxito.
  ```json
  {
    "price_indexed": 95.4215,
    "total_importes": 1500.50,
    "total_consumos": 15.725
  }
  ```

- **`400 Bad Request`**: Datos proporcionados inválidos o incompletos.
  - Falta `start_date`, `end_date` o `formula`.
  - Formato de fecha inválido o `start_date > end_date`.
  - La fórmula **no incluye** la etiqueta obligatoria `[OMIE_MD]`.

- **`404 Not Found`**: No existen registros completos en la base de datos (mediante Eloquent) para **todo** el rango de fechas especificado entre `start_date` y `end_date`.

- **`500 Internal Server Error`**: Ocurrió un error inesperado al procesar los datos o al evaluar la fórmula.

---

## 🧮 6. LÓGICA DE CÁLCULO DE NEGOCIO

1. Consultar únicamente mediante Eloquent ORM:
   - `Consumption::betweenDates($startDate, $endDate)->get()->keyBy(fn($item) => $item->date->format('Y-m-d'));`
   - `Price::betweenDates($startDate, $endDate)->get()->keyBy(fn($item) => $item->date->format('Y-m-d'));`
2. Verificar que exista un registro de consumo y de precio para **todos y cada uno de los días del rango**. Si falta algún día, retornar `404 Not Found`.
3. Para cada día del rango y para cada hora `h1` a `h25`:
   - Reemplazar `[OMIE_MD]` por el precio horario correspondiente.
   - Evaluar con la librería `mossaden/math-executor` de forma segura (NUNCA usar `eval()`).
   - `importe_hora = valor_formula_evaluado * consumo_hora`
   - Acumular `suma_importes += importe_hora` y `suma_consumos += consumo_hora`.
4. Obtener precio indexado: `precio_indexado = suma_importes / suma_consumos`.

---

## 📝 7. DOCUMENTACIÓN Y README DE GIT (BACKEND)

- Mantenimiento estricto del archivo `backend/README.md`.
- Debe detallar requisitos (`PHP 8.4.4`, `MySQL`), comandos de instalación (`composer install`), configuración de variables de entorno (`.env`), migraciones, seeders, ejecución de tests (`php artisan test`) y regeneración de Swagger UI (`php artisan l5-swagger:generate`).

---

## 🛠️ 8. REGLAS DE CÓDIGO Y SEGURIDAD

- **PHP 8.4.4 Estricto**: Tipado completo en parámetros y retornos de métodos.
- **Eloquent ORM Exclusivo**: Sin consultas SQL crudas.
- **Swagger al Día**: Actualizar anotaciones y regenerar docs al modificar rutas o schemas.
- **README Actualizado**: Actualizar `backend/README.md` tras cada hito o nueva característica instalada.
- **Seguridad en Fórmulas**: Procesamiento mediante AST/MathParser (`mossaden/math-executor`), prohibido `eval()`.
- **Soporte Local y Producción**: Asegurarse en todo momento de que el código funciona tanto en local como en producción. Separar correctamente y de manera optimizada ambos entornos (cuidando especialmente variables `.env`, integración en CI/CD y dependencias de testing).
