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
    // URL de descarga del respaldo.
    private $url;
    // Almacena mensaje de error en caso de que suceda.
    private $error;

    /**
     * Constructor de la clase Respaldador
     * @since  Enero 2014
     * @param  string $ruta       Ruta donde están los archivos
     * @param  string $directorio Directorio de los respaldos
     * @param  string $url        URL de descarga
     */
    public function Respaldador($nombre, $directorio, $ruta = '') {
        //@todo Validador de ruta
        $this->ruta = ($ruta) ? $ruta : $_SERVER['DOCUMENT_ROOT'];

        if (!$this->setDirectorio($directorio))
            throw new Exception($this->error);

        if(!$this->setNombre($nombre))
            throw new Exception($this->error);

        ini_set('max_execution_time', 3000);
    }

    /*
     * Realiza el respaldo del sitio en formato zip.
     */
    public function respaldar() {
        if(!empty($this->directorio) && !empty($this->nombre) && $this->generateArchivo()){
          $respaldo = new ZipArchive();

          if ($respaldo->open($this->archivo, ZIPARCHIVE::CREATE) !== true ) {
              $this->error = "No se puede crear archivo .zip que almacenara el respaldo";
              return false;
          }

          $this->comprimir($this->ruta, $respaldo);

          $respaldo->close();

          $this->url = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $this->directorio . '/' . $this->nombre . '.zip';
          $this->error = '';
        }else{
          $this->error = 'No se puede crear respaldo, ya que no ha sido configurado el atributo directorio o nombre.';
          return false;
        }
    }


    /**
     * Obtiene el ultimo mensaje de error generado.
     *
     * @return String Mensaje de error
     */
    public function getError(){
        return $this->error;
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

    /**
     * Permite crear el nombre del archivo incluyendo las ruta y directorio
     * @author Tomás Hernández <tomas.hernandez03@gmail.com>
     * @since  Enero 2014
     * @return string
     */
    private function generateArchivo(){
        $archivo = $this->ruta . DIRECTORY_SEPARATOR . $this->directorio . DIRECTORY_SEPARATOR . $this->nombre . '.zip';

        if ($this->validateArchivo($archivo)) {
            $this->archivo = $archivo;
            return true;
        } else {
            $this->archivo = "";
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
                $this->error = "No se puede crear directorio para guardar respaldo.";
                return false;
            }
        }

        // Validar la escritura en el directorio.
        if (!is_writable($ruta)) {
            $this->error = "El directorio no permite la escritura de archivos.";
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
            $this->error = "El atributo directorio no ha sido configurado.";
            return false;
        }

    /**
     * Contiene todas las validaciones que se deben realizar antes de poceder a
     * setearlas a la propiedad de la clase
     * @author Tomás Hernández <tomas.hernandez03@gmail.com>
     * @since  Enero 2014
     * @param  string $archivo ruta + directorio + nombre del archivo que se
     *                         quiere setear
     * @return boolean
     */
    private function validateArchivo(&$archivo){
      if (file_exists($archivo)) {
          $this->error = "Ya existe un respaldo con ese nombre en el directorio.";
          return false;
      } else {
          return true;
      }

    private function comprimir($dir, &$zip) {

        if (is_dir($dir)) {
            foreach (scandir($dir) as $item) {
                if ($item == '.' || $item == '..' || $item == $this->directorio)
                    continue;
                $this->comprimir($dir . DIRECTORY_SEPARATOR . $item, $zip);
            }
        }else{
            $zip->addFile($dir);
        }
    }
}

?>
