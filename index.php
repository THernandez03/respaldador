<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Prueba de clase Respaldador</title>
    </head>
    <body>
        <?php
        include("Respaldador.php");
        
        //@todo Mostrar errores generados por la clase
        
        /********************************************************************
         * Prueba de clase Respaldo con constructor
         ********************************************************************/

        // Crear clase
        $respaldador = new Respaldador("Respaldo05", "respaldos");
        // Respaldando archivos
        $respaldador->respaldar();

        // Mostrando URL de descarga del respaldo creado
        echo "La URL de descarga del respaldo es: " .  $respaldador->getURL() . "<br/>";
        ?>
    </body>
</html>
