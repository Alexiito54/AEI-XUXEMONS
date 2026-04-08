-- Inicialización de la base de datos XUXEMONS
-- Este script se ejecuta automáticamente cuando se inicia MySQL en Docker

CREATE DATABASE IF NOT EXISTS `bd-xuxemons`;

USE `bd-xuxemons`;

-- Las migraciones de Laravel crearán todas las tablas necesarias
-- Este archivo está aquí como referencia y para asegurar que la BD existe
