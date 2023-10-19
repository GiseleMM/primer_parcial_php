<?php

/**autoBD.php. Crear, en ./clases, la clase AutoBD (hereda de Auto) con atributo protegido:
● pathFoto(cadena)
Un constructor (con parámetros opcionales), un método de instancia toJSON(), que retornará los datos de la
instancia (en una cadena con formato JSON).
Crear, en ./clases, la interface IParte1. Esta interface poseerá los métodos:
● agregar: agrega, a partir de la instancia actual, un nuevo registro en la tabla autos (patente, marca, color,
precio, foto), de la base de datos garage_bd. Retorna true, si se pudo agregar, false, caso contrario.
● traer: este método estático retorna un array de objetos de tipo AutoBD, recuperados de la base de datos */

namespace Medina;

use Exception;
use PDO;
use PDOException;
use stdClass;

require_once "./clases/auto.php";
require_once "./clases/iparte1.php";
require_once "./clases/iparte2.php";
require_once "./clases/iparte3.php";
class AutoBD extends Auto implements IParte1, IParte2, IParte3
{
    //public string $pathFoto;
    protected string $pathFoto;
    public function __construct($patente, $marca, $color, $precio, $foto = "")
    {
        parent::__construct($patente, $marca, $color, $precio);
        $this->pathFoto = $foto;
    }


    //IMPLEMENTACION DE IPARTE3
    /**● existe: retorna true, si la instancia actual está en el array de objetos de tipo AutoBD que recibe como
parámetro (comparar por patente). Caso contrario retorna false.
● guardarEnArchivo: escribirá en un archivo de texto (./archivos/autosbd_borrados.txt) toda la información
del auto más la nueva ubicación de la foto. La foto se moverá al subdirectorio “./autosBorrados/”, con el
nombre formado por la patente punto 'borrado' punto hora, minutos y segundos del borrado (Ejemplo:
AYF714.renault.borrado.105905.jpg).
Se retornará un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido  */
    public function existe(array $array_autoBd): bool
    {
        $existe = false;
        if (isset($array_autoBd)) {
            foreach ($array_autoBd as $key => $value) {
                if ($value->patente === $this->patente) {
                    $existe = true;
                    break;
                }
            }
        }
        return $existe;
    }
    //me sirve en el listado
    public function toJSON(): string
    {
      $std=new stdClass();
      $std->patente=$this->patente;
      $std->marca=$this->marca; 
      $std->color=$this->color;
      $std->precio=$this->precio;
      $std->pathFoto=$this->pathFoto;
      return  json_encode($std,JSON_PRETTY_PRINT);
    }
    public function guardarEnArchivo(): string
    {
        $obj = new stdClass();
        $file= dirname(__DIR__);
        $file=str_replace("\\","/",$file);

        $path_txt = $file."/archivos/autosbd_borrados.txt";
      //  echo __DIR__ . "/backend/archivos/autosBorrados/";
      
        if (!is_dir(dirname(__DIR__) . "/archivos/autosBorrados/")) {
            mkdir(dirname(__DIR__). "/archivos/autosBorrados/", 0777, true);
        }
        $destino = "./archivos/autosBorrados/$this->patente.$this->marca.borrado." . date("Hms") . "." . pathinfo($this->pathFoto, PATHINFO_EXTENSION);
        //echo "DESTINO".$destino."</br>";
        $buffer=trim($this->pathFoto);
        $foto_array=explode("/",$buffer);
       // echo end($foto_array)."</br>";
        
        //echo __DIR__."./../archivos/autos/fotos/".end($foto_array)."</br>";
      
        if (copy("$file/autos/fotos/".end($foto_array), $destino)) {

           //cambio foto para guardar foto con nueva direccion
            $this->pathFoto="$this->patente.$this->marca.borrado." . date("Hms") . "." . pathinfo($this->pathFoto, PATHINFO_EXTENSION);
           
            unlink("$file/autos/fotos/".end($foto_array));
            $array = array();
            try {
                $contenido = file_get_contents($path_txt);
                if ($contenido === false) {
                    array_push($array, $this);
                } else {

                    $buffer = json_decode($contenido);

                    $array = array_map(function ($s) {
                        $aux = new AutoBD($s->patente, $s->marca, $s->color, $s->precio, $s->pathFoto);
                        return $aux;
                    }, $buffer);
                    array_push($array, $this);
                    // var_dump($array);
                }

                if (file_put_contents($path_txt, json_encode($array, JSON_PRETTY_PRINT))) {
                    $obj->exito = "true";
                    $obj->mensaje = "Guardado en archivo $path_txt";
                } else {
                    $obj->exito = "false";
                    $obj->mensaje = "Error guardado en archivo $path_txt";
                }
            } catch (Exception $th) {
                $obj->exito = "false";
                $mensaje = $th->getMessage();
                $obj->mensaje = "Excepcion en guardado en archivo: $mensaje";
            }
        } else {

            $obj->exito = false;

            $obj->mensaje = " NO se pudo guardar en archivo ni mover foto";
        }
        return json_encode($obj, JSON_PRETTY_PRINT);
    }

    // public function get_foto()
    // {
    //     $array=explode("/",$this->foto);
    //     var_dump($array);
    //     return $this->foto;
    // }
    public function set_foto()
    {
        $errores = [];

        $destino = dirname(__DIR__);
        $destino = str_replace('\\',  '/', $destino);
        $destino .= "/autos/fotos/";
        if (!is_dir($destino)) {
            mkdir($destino, 0777, true);
        }


        $tamMax = 100000;
        $aux = isset($_FILES["foto"]) ? $_FILES["foto"] : null;
        if (isset($aux)) {


            if ($aux["error"] !== 0) {
                array_push($errores, "error en subida de archivo");
            }

            $array_imagen = getimagesize($aux["tmp_name"]);
            if ($array_imagen === false) {
                array_push($errores, "tipo no valido,no es una imagen");
            }
            if ($aux["size"] > $tamMax) {
                array_push($errores, "imagen supera los 4000");
            }
            if (count($errores) == 0) {

                $extension = pathinfo($aux["name"], PATHINFO_EXTENSION);

                $archivo = "$this->patente." . date("His") . "." . $extension;
                $destino .= $archivo;
                var_dump($aux["tmp_name"]);
                echo "DESTINO: $destino";

                move_uploaded_file($aux["tmp_name"], $destino);
                $this->pathFoto = "./$archivo";
            } else {

                var_dump($errores);
                $this->pathFoto = "";
            }
        }
    }


    /*Agregar (de instancia): agrega, a partir de la instancia actual, un nuevo registro en la tabla empleados
(id,patente, marca, color, precio, foto y sueldo), de la base de datos usuarios_test. Retorna true, si se pudo
agregar, false, caso contrario. */
    public function agregar(): bool
    {
        $agregado = false;
        try {


            $pdo = new PDO("mysql:host=localhost;dbname=garage_bd", "root", "");
            $sql = $pdo->prepare("INSERT INTO autos (patente,marca,color,precio,foto) VALUES(:patente,:marca,:color,:precio,:foto);");
            $sql->bindParam(":patente", $this->patente, PDO::PARAM_STR);
            $sql->bindParam(":marca", $this->marca, PDO::PARAM_STR);
            $sql->bindParam(":color", $this->color, PDO::PARAM_STR);
            $sql->bindParam(":precio", $this->precio, PDO::PARAM_STR);
            $sql->bindParam(":foto", $this->pathFoto, PDO::PARAM_STR);


            if ($sql->execute()) {
                $agregado = true;
            }
        } catch (PDOException $th) {
            echo $th->getMessage();
            $agregado = false;
        }
        return $agregado;
    }

    /*
    modificar: Modifica en la base de datos el registro coincidente con la instancia actual (comparar por
patente). Retorna true, si se pudo modificar, false, caso contrario. */
    public function modificar(): bool
    {
        $modificado = false;
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=garage_bd", "root", "");
            if ($this->pathFoto !== "") {
                //UPDATE `empleados` SET `id`='[value-1]',`marca`='[value-2]',`color`='[value-3]',`patente`='[value-4]',`precio`='[value-5]',`foto`='[value-6]',`sueldo`='[value-7]' WHERE 1
                $sql = $pdo->prepare("UPDATE autos SET marca=:marca,color=:color,precio=:precio,foto=:foto WHERE patente=:patente;");
                $sql->bindParam(":foto", $this->pathFoto, PDO::PARAM_STR);
            } else {
                $sql = $pdo->prepare("UPDATE autos SET marca=:marca,color=:color,precio=:precio WHERE patente=:patente;");
            }
            $sql->bindParam(":patente", $this->patente, PDO::PARAM_STR);
            $sql->bindParam(":marca", $this->marca, PDO::PARAM_STR);
            $sql->bindParam(":color", $this->color, PDO::PARAM_STR);
            $sql->bindParam(":precio", $this->precio, PDO::PARAM_STR);

            if ($sql->execute()) {
                $modificado = true;
            }
        } catch (PDOException $th) {
            echo $th->getMessage();
        }


        return $modificado;
    }
    /*
● eliminar: este método estático, elimina de la base de datos el registro coincidente con la patente recibida
cómo parámetro. Retorna true, si se pudo eliminar, false, caso contrario.*/

    public static function eliminar($patente): bool
    {
        $eliminado = false;
        if (isset($patente)) {
          //  echo "entrea eliminar";
            try {
                $pdo = new PDO("mysql:host=localhost;dbname=garage_bd", "root", "");
                $sql = $pdo->prepare("DELETE FROM autos WHERE patente=:patente;");
                $sql->bindParam(":patente", $patente, PDO::PARAM_STR);
                if ($sql->execute()) $eliminado = true;
            } catch (PDOException $th) {
                echo $th->getMessage();
                $eliminado = false;
            }
        }
        return $eliminado;
    }
    public static function traer(): array
    {
        $array = [];
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=garage_bd", "root", "");
            $sql = $pdo->prepare("SELECT * from autos");
            //SELECT `id`, `marca`, `color`, `patente`, `precio`, `foto`, `sueldo` FROM `empleados` WHERE 1
            $sql->execute();
            while ($fila = $sql->fetchObject()) {

                if (isset($fila->foto)) {

                    $aux = new AutoBD($fila->patente, $fila->marca, $fila->color, (float)$fila->precio, $fila->foto);
                } else {
                    $aux = new AutoBD($fila->patente, $fila->marca, $fila->color, (float)$fila->precio);
                }
                array_push($array, $aux);
            }
        } catch (PDOException $ex) {
            echo $ex->getMessage();
            $array = null;
        }
        return $array;
    }
}
