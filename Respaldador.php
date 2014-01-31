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
  // Filtro de directorios y extensiones a excluir del respaldo.
  private $filtros;
  // Mensajes de error en el idioma configurado
  private $errores;

  /**
   * Constructor de la clase Respaldador
   * @author Fiko Bórquez <darkshinjis@gmail.com>
   * @since  Enero 2014
   * @param  string $nombre Nombre que tendra el rspaldo.
   * @param  string $directorio Directorio de los respaldos.
   * @param  string $ruta Ruta del servidor web en el sistema.
   * @param  array $filtros Filtros de directorios y extensiones a excluir.
   * @param  string $idioma Idioma para los mensajes de error
   */
  public function Respaldador($nombre, $directorio, $ruta = '', $filtros = array(), $idioma = 'es') {
    //@todo Validador de ruta
    $this->ruta = ($ruta) ? $ruta : $_SERVER['DOCUMENT_ROOT'];

    if(!$this->setDirectorio($directorio)){
      throw new Exception($this->error);
    }

    if(!$this->setNombre($nombre)){
      throw new Exception($this->error);
    }
        
    $this->filtros = $filtros;

    ini_set('max_execution_time', 3000);
        
    if(file_exists('idiomas/' . $idioma . '.php')){
      include 'idiomas/' . $idioma . '.php';
    }else{
      $this->error = 'El archivo para idioma configurado no existe.';
      throw new Exception($this->error);
    }
  }

  /**
   * Realiza el respaldo del sitio en formato zip.
   * @author Fiko Bórquez <darkshinjis@gmail.com>
   * @since Enero 2014
   * @return boolean
   */
  public function respaldar() {
    if(!empty($this->directorio) && !empty($this->nombre) && $this->generateArchivo()){
      $respaldo = new ZipArchive();

      if($respaldo->open($this->archivo, ZIPARCHIVE::CREATE) !== true ){
        $this->error = $this->errores['error_creacion'];
        return false;
      }

      $this->comprimir($this->ruta, $respaldo);

      $respaldo->close();

      $this->url = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $this->directorio . '/' . $this->nombre . '.zip';
      $this->error = '';
      return true;
    }else{
      $this->error = $this->errores['error_atributos'];
      return false;
    }
  }


  /**
   * Obtiene el ultimo mensaje de error generado.
   * @author Fiko Bórquez <darkshinjis@gmail.com>
   * @since Enero 2014
   * @return string Ultimo mensaje de error
   */
  public function getError(){
    return $this->error;
  }

  /**
   * Configura el nombre que tendra el respaldo.
   * @author Fiko Bórquez <darkshinjis@gmail.com>
   * @since Enero 2014
   * @param string $nombre Nombre del respaldo.
   * @return boolean
   */
  public function setNombre($nombre){
    if($this->validateNombre($nombre)){
      $this->nombre = $nombre;
      return true;
    }else{
      $this->nombre = "";
      return false;
    }
  }

  /**
   * Obtiene el nombre que tendra el respaldo.
   * @author Fiko Bórquez <darkshinjis@gmail.com>
   * @since Enero 2014
   * @return string Nombre del respaldo.
   */
  public function getNombre(){
    return $this->nombre;
  }

  /**
   * Configura el directorio dentro de la carpeta contenedora
   * de archivos del servidor donde se guardaran los respaldos.
   * @author Fiko Bórquez <darkshinjis@gmail.com>
   * @since Enero 2014
   * @param string $directorio Directorio contenedor de respaldos.
   * @return boolean
   */
  public function setDirectorio($directorio){
    if($this->validateDirectorio($directorio)){
      $this->directorio = $directorio;
      return true;
    }else{
      $this->nombre = "";
      return false;
    }
  }

  /**
   * Permite crear el nombre del archivo incluyendo las ruta y directorio
   * @author Tomás Hernández <tomas.hernandez03@gmail.com>
   * @since  Enero 2014
   * @return boolean
   */
  private function generateArchivo(){
    $archivo = $this->ruta . DIRECTORY_SEPARATOR . $this->directorio . DIRECTORY_SEPARATOR . $this->nombre . '.zip';

    if($this->validateArchivo($archivo)){
      $this->archivo = $archivo;
      return true;
    }else{
      $this->archivo = "";
      return false;
    }
  }

  /**
   * Obtiene el directorio que contendra los respaldos.
   * @author Fiko Bórquez <darkshinjis@gmail.com>
   * @since Enero 2014
   * @return string Directorio conenedor de respaldos
   */
  public function getDirectorio(){
    return $this->directorio;
  }

  /**
   * Obtiene la url de descarga del respaldo.
   * @author Fiko Bórquez <darkshinjis@gmail.com>
   * @since Enero 2014
   * @return string URL de descarga del respaldo
   */
  public function getURL(){
    return $this->url;
  }

  /**
   * Valida que el directorio cumpla condiciones dadas.
   * @author Fiko Bórquez <darkshinjis@gmail.com>
   * @since Enero 2014
   * @param string $directorio Directorio a validar.
   * @return boolean
   */
  private function validateDirectorio(&$directorio){
    // @todo realizar saneamiento de nombre de directorio segun S.O

    $directorio = trim($directorio);
    $ruta = $this->ruta . DIRECTORY_SEPARATOR . $directorio;

    if(!file_exists($ruta)){
      if(!mkdir($ruta)){
        $this->error = $this->errores['error_creacion_directorio'];
        return false;
      }
    }

    if(!is_writable($ruta)){
      $this->error = $this->errores['error_escritura_directorio'];
      return false;
    }else{
      return true;
    }
  }

  /**
   * Valida que el nombre cumpla condiciones dadas.
   * Tambien realiza saneamiento.
   * @author Fiko Bórquez <darkshinjis@gmail.com>
   * @since  Enero 2014
   * @param  string &$nombre Nombre que tendra el archivo de respaldo.
   * @return boolean
   */
  private function validateNombre(&$nombre){
    // @todo realizar saneamiento de nombre de archivo segun S.O

    $nombre = trim($nombre);
    
    if(empty($this->directorio)){
      $this->error = $this->errores['error_atributo_directorio'];
      return false;
    }else{
      return true;
    }
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
    if(file_exists($archivo)){
      $this->error = $this->errores['error_archivo_existe'];
      return false;
    }else{
      return true;
    }
  }

  /**
   * Agrega archivos y directorios a archivo zip de respaldo.
   * @author Fiko Bórquez <darkshinjis@gmail.com>
   * @since Enero 2014
   * @param string $dir Archivo o directorio a agregar.
   * @param resource $zip Archivo zip que almacena el respaldo
   */
  private function comprimir($dir, &$zip) {
    
    if(is_dir($dir)){
      foreach(scandir($dir) as $item){
        if($item == '.' || $item == '..' || $item == $this->directorio || $this->filtrarDirectorio($dir)){
          continue;
        }
        
        $this->comprimir($dir . DIRECTORY_SEPARATOR . $item, $zip);
      }
    }else{
      if(!$this->filtrarExtension($dir)){
        $zip->addFile($dir);
      }
    }
  }
    
  /**
   * Realiza el filtro de directorios a excluir del respaldo.
   * @author Fiko Bórquez <darkshinjis@gmail.com>
   * @since  Enero 2014
   * @param string $dir El directorio a evaluar si aplica filtro.
   * @return boolean
   */
  private function filtrarDirectorio($dir){
    $dir = str_replace($this->ruta, '', $dir);
      
    if($dir === ''){
      $dir = '\\';
    }
      
      return $this->filtrar($dir, '*');
  }
    
  /**
   * Realiza el filtro de extensiones dentro de un directorio a excluir del respaldo.
   * @author Fiko Bórquez <darkshinjis@gmail.com>
   * @since  Enero 2014
   * @param string $archivo Archivo dentro de un directorio para aplicar filtro segun extension.
   * @return boolean
   */
  private function filtrarExtension($archivo){
    $dir = dirname(str_replace($this->ruta, '', $archivo));
    $extension = explode('.', $archivo);
    $extension = end($extension);
      
    return $this->filtrar($dir, $extension);
  }
    
  /**
   * Compara combinatoria directorio/extension para saber si aplica filtro.
   * @author Fiko Bórquez <darkshinjis@gmail.com>
   * @since  Enero 2014
   * @param string $dir Directorio al que se le aplica filtro.
   * @param string $ext Extension a filtrar.
   * @return boolean
   */
  private function filtrar($dir, $ext){
    if(key_exists($dir, $this->filtros)){
      if(in_array($ext, $this->filtros[$dir])){
        return true;
      }else{
        return false;
      }
    }else{
      return false;
    }
  }
}
?>
