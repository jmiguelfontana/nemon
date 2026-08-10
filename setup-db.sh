#!/bin/bash
# -----------------------------------------------------------------------------
# Script para automatizar la creación del usuario nemon_app en un MySQL existente
# -----------------------------------------------------------------------------
# Este script ataca directamente al contenedor 'nemon_db' que ya está encendido.
# Es ideal para cuando el volumen de la base de datos ya existía de antes y 
# Docker no lee las variables de entorno de creación por primera vez.

echo "Conectando al contenedor nemon_db y configurando el usuario..."

# 1. Determinar la contraseña del nuevo usuario de la aplicación
APP_PASS=${1:-nemon_secret}

# 2. Crear el usuario (usando 'root' como contraseña de root)
docker exec -i nemon_db mysql -u root -p'root' <<< "
CREATE DATABASE IF NOT EXISTS nemon_energy;
CREATE USER IF NOT EXISTS 'nemon_app'@'%' IDENTIFIED BY '$APP_PASS';
GRANT ALL PRIVILEGES ON nemon_energy.* TO 'nemon_app'@'%';
FLUSH PRIVILEGES;
"

echo "¡Usuario nemon_app configurado con éxito!"
