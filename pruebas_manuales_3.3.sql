-- -----------------------------------------------------------------------------
-- ARCHIVO DE PRUEBAS MANUALES - REQUISITO 3.3
-- -----------------------------------------------------------------------------
-- Este archivo contiene sentencias INSERT preparadas para que los evaluadores 
-- puedan probar la inserción manual de datos de consumo y precios en su entorno local.
-- -----------------------------------------------------------------------------

USE nemon_energy;

-- 1. Insertar Consumos (Ejemplo: 15 de Marzo de 2025 con consumos escalonados)
INSERT INTO consumptions 
(date, h1, h2, h3, h4, h5, h6, h7, h8, h9, h10, h11, h12, h13, h14, h15, h16, h17, h18, h19, h20, h21, h22, h23, h24, h25, created_at, updated_at) 
VALUES 
('2025-03-15', 5.5, 5.2, 5.0, 4.8, 5.1, 7.0, 10.5, 12.0, 11.5, 15.0, 14.5, 16.0, 18.2, 17.5, 15.0, 14.2, 13.5, 18.0, 22.5, 25.0, 24.5, 15.0, 10.0, 8.5, 0.0, NOW(), NOW());

-- 2. Insertar Precios OMIE_MD (Ejemplo: 15 de Marzo de 2025)
INSERT INTO prices 
(date, h1, h2, h3, h4, h5, h6, h7, h8, h9, h10, h11, h12, h13, h14, h15, h16, h17, h18, h19, h20, h21, h22, h23, h24, h25, created_at, updated_at) 
VALUES 
('2025-03-15', 0.08, 0.07, 0.07, 0.06, 0.06, 0.09, 0.12, 0.15, 0.14, 0.11, 0.09, 0.08, 0.08, 0.09, 0.10, 0.11, 0.14, 0.18, 0.22, 0.25, 0.21, 0.15, 0.12, 0.09, 0.00, NOW(), NOW());

-- NOTA: Una vez ejecutados estos INSERTS en tu base de datos local, 
-- puedes ir a la interfaz web y pedir el cálculo para el 15 de Marzo de 2025 
-- para comprobar si los datos se han inyectado y calculado correctamente.
