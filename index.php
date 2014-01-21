<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Prueba de clase Respaldador</title>
    </head>
    <body>
        <?php
        include("Respaldador.php");

        /********************************************************************
         * Prueba de clase Respaldo con constructor
         ********************************************************************/

        // Instanciar clase
        try {
            $respaldador = new Respaldador("Respaldo05", "respaldos");
            $respaldador->respaldar();
            
            if ($respaldador->getError() == ''){
                // Mostrar url de descarga
                echo "La URL de descarga del respaldo es: " .  $respaldador->getURL() . "<br/>";
            } else {
                // Mostrar errores causados luego de la instancia de la clase
                echo $respaldador->getError();
            }
        } catch (Exception $e) {
            // Mostrar errores causados durante la instancia de la clase
            echo $e->getMessage();
        }
        ?>
    </body>
</html>
