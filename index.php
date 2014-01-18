<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Prueba de clase Respaldador</title>
    </head>
    <body>
        <?php
        include("Respaldador.php");
        
        $respaldador = new Respaldador();
        $nombre = "Respaldo05";
        $directorio = "respaldos";
        
        // Configurando directorio del respaldo.
        
        if ($respaldador->setDirectorio($directorio)) {
            echo "Nombre directorio: " . $respaldador->getDirectorio() . "<br/>";
        } else {
            echo "No se pudo configurar directorio de respaldo <b>" . $directorio . "</b><br/>";
        }
        
        if ($respaldador->setNombre($nombre)) {
            echo "Nombre respaldo: " . $respaldador->getNombre() . "<br/>";
        } else {
            echo "No se pudo configurar nombre de respaldo <b>" . $nombre . "</b><br/>";
        }
        
        // Respaldando archivos
        $respaldador->respaldar();
        
        // Mostrando URL de descarga del respaldo creado
        echo "La URL de descarga del respaldo es: " .  $respaldador->getURL() . "<br/>";
        
        
        ?>
    </body>
</html>
