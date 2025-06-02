# Solución para el Historial Médico

Este documento contiene instrucciones para solucionar el problema con la visualización del historial médico desde el perfil del paciente.

## Problema

Al intentar visualizar el historial médico desde el perfil del paciente, no se muestra nada y aparece el mensaje "Ocurrió un error al cargar el historial del paciente".

## Causas del Problema

1. **Configuración incorrecta de la base de datos**: La contraseña de la base de datos podría estar incorrecta.
2. **Tablas faltantes**: Las tablas necesarias para el historial médico podrían no existir en la base de datos.

## Solución

### 1. Verificar la configuración de la base de datos

Asegúrate de que la configuración de la base de datos en `config/database.config.php` sea correcta:

```php
$host     = "localhost:3306";
$dbname   = "medic_life";
$username = "root";
$password = ""; // Para XAMPP, la contraseña por defecto suele estar vacía
```

### 2. Crear las tablas necesarias

Hemos creado un script que creará automáticamente las tablas necesarias para el historial médico. Sigue estos pasos:

1. Abre tu navegador web
   2. Navega a: `http://localhost/newMedicLife/create-medical-history-tables.php`
3. Si todo va bien, verás un mensaje de éxito indicando que las tablas se han creado correctamente.

### 3. Instalar TCPDF (opcional, para generar PDFs)

Si deseas generar PDFs del historial médico, necesitas instalar la biblioteca TCPDF:

1. Abre una terminal o línea de comandos
2. Navega al directorio del proyecto: `cd C:\xampp\htdocs\newMedicLife`
3. Ejecuta el siguiente comando: `composer require tecnickcom/tcpdf`

### 4. Probar la funcionalidad

Una vez completados los pasos anteriores:

1. Inicia sesión en la aplicación
2. Navega al perfil de un paciente
3. Haz clic en el botón "Historial Médico"
4. Ahora deberías poder ver y crear registros en el historial médico

## Estructura de las Tablas

El historial médico utiliza las siguientes tablas:

1. **medical_history**: Almacena los registros principales del historial médico
2. **vital_signs_history**: Almacena los signos vitales para cada registro del historial
3. **medical_attachments**: Almacena los archivos adjuntos (como PDFs) para cada registro

## Soporte

Si continúas teniendo problemas, verifica los logs de error de PHP en `C:\xampp\php\logs` o en `C:\xampp\apache\logs` para obtener más información sobre el error.