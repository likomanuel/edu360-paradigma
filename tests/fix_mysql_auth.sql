-- Script para solucionar el error de autenticación MySQL
-- Ejecuta estos comandos en tu cliente MySQL (phpMyAdmin, MySQL Workbench, o línea de comandos)

-- OPCIÓN 1: Cambiar el método de autenticación del usuario root
-- Esto cambia de caching_sha2_password a mysql_native_password
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'a10882990';
FLUSH PRIVILEGES;

-- OPCIÓN 2: Si usas otro usuario, reemplaza 'root' con tu usuario
-- ALTER USER 'tu_usuario'@'localhost' IDENTIFIED WITH mysql_native_password BY 'tu_contraseña';
-- FLUSH PRIVILEGES;

-- OPCIÓN 3: Verificar qué plugin de autenticación está usando tu usuario
SELECT user, host, plugin FROM mysql.user WHERE user = 'root';

-- NOTA: Después de ejecutar estos comandos, reinicia Apache para que tome los cambios
