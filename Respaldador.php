<?php

/**
 * Genera respaldos en zip de los archivos en un servidor.
 *
 * @author Fiko
 */
class Respaldador {
    // Nombre que tendra el respaldo.
    private $nombre;
    // Ruta donde estan los archivos del servidor web.
    private $ruta;
    // Directorio donde se guardaran los respaldos.
    private $directorio;
    // URL de descarga del respaldo
    private $url;
    
    public function Respaldador() {
        $this->ruta = $_SERVER['DOCUMENT_ROOT'];
        $this->directorio = "";
        $this->url = "";
        
        ini_set('max_execution_time', 3000);
    }
    
    /*
     * Realiza el respaldo del sitio en formato zip.
     */
    public function respaldar() {
        if (empty($this->directorio) || empty($this->nombre)) {
            return false;
        }
        
        $respaldo = new ZipArchive();
        $archivo = $this->ruta . DIRECTORY_SEPARATOR . $this->directorio . DIRECTORY_SEPARATOR . $this->nombre . '.zip';
        
        if ($respaldo->open($archivo, ZIPARCHIVE::CREATE) !== true ) {
            return false;
        }
        
        $this->comprimir($this->ruta, $respaldo);
        
        $respaldo->close();
        
        
        $this->url = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $this->directorio . '/' . $this->nombre . '.zip';
        
    }
    
    /*
     * Configura el nombre que tendra el respaldo
     */
    public function setNombre($nombre){
        if ($this->validateNombre($nombre)) {
            $this->nombre = $nombre;
            return true;
        } else {
            $this->nombre = "";
            return false;
        }
    }
    
    /*
     * Obtiene el nombre que tendra el respaldo
     */
    public function getNombre(){
        return $this->nombre;
    }
    
    /*
     * Configura el directorio dentro de la carpeta contenedora
     * de archivos del servidor donde se guardaran los respaldos.
     */
    public function setDirectorio($directorio){
        if ($this->validateDirectorio($directorio)) {
            $this->directorio = $directorio;
            return true;
        } else {
            $this->nombre = "";
            return false;
        }
    }
    
    /*
     * Obtiene el directorio que contendra los respaldos.
     * No hay que especificar la ruta al directorio, solo
     * su nombre.
     * Ejemplo: respaldos
     */
    public function getDirectorio(){
        return $this->directorio;
    }
    
    /**
     * Obtiene la url de descarga del respaldo.
     * 
     * @return String
     */
    public function getURL() {
        return $this->url;
    }
    
    /*
     * Valida que el directorio cumpla condiciones dadas
     */
    private function validateDirectorio(&$directorio) {
        // @todo realizar saneamiento de nombre de directorio segun S.O
        
        $directorio = trim($directorio);
        $ruta = $this->ruta . DIRECTORY_SEPARATOR . $directorio;
        
        // Validar que el directorio exista, sino crearlo.
        // Si no se puede crear el directorio, entonces termina
        // la ejecucion del metodo.
        
        if (!file_exists($ruta)) {
            if (!mkdir($ruta)) {
                return false;
            }
        }
        
        // Validar la escritura en el directorio.
        if (!is_writable($ruta)) {
            return false;
        } else {
            return true;
        }
    }
    
    /*
     * Valida que el nombre cumpla condiciones dadas
     */
    private function validateNombre(&$nombre) {
        // @todo realizar saneamiento de nombre de archivo segun S.O
        
        $nombre = trim($nombre);
        
        // Validar que el directorio este configurado en la clase
        if (empty($this->directorio)) {
            echo 1;
            return false;
        }
        
        
        // Validar que no exista un respaldo con ese nombre
        $archivo = $this->ruta . DIRECTORY_SEPARATOR . $this->directorio . DIRECTORY_SEPARATOR . $nombre . '.zip';
        
        if (file_exists($archivo)) {
            var_dump($archivo);
            return false;
        } else {
            return true;
        }
    }
    
    private function comprimir($dir, &$zip) {  
        
        if (is_dir($dir)) {  
            foreach (scandir($dir) as $item) {
                if ($item == '.' || $item == '..') 
                    continue;
                $this->comprimir($dir . DIRECTORY_SEPARATOR . $item, $zip);  
            }  
        }else{ 
            $zip->addFile($dir);  
        }  
    }  
}

?>
