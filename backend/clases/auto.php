<?php
//require_once "ibm.php";
namespace Medina;

use stdClass;
use Exception;

class Auto
{

    // public string $patente;
    // public string $marca;
    // public string $color;
    // public float $precio;

//correccion 
    protected string $patente;
    protected string $marca;
    protected string $color;
    protected float $precio;

    public function __construct(string $patente, string $marca, string $color, float $precio)
    {
        $this->patente = $patente;
        $this->marca = $marca;
        $this->color = $color;
        $this->precio = $precio;
    }
    public function toJSON(): string
    {
        $std= new stdClass();
        $std->patente=$this->patente;
        $std->marca=$this->marca;
        $std->color=$this->color;
        $std->precio=$this->precio;
        return json_encode($std, JSON_PRETTY_PRINT);
    }
    public function guardarJSON(string $path): string
    {
        $obj = new stdClass();
        $array = array();

        try {
            //usa estandar class por q json no seriliza atributos protegidos
            $std= new stdClass();
            $std->patente=$this->patente;
            $std->marca=$this->marca;
            $std->color=$this->color;
            $std->precio=$this->precio;
//var_dump($std);

            //obtengo todo lo del archivo
            $contenido = file_get_contents($path);
            if ($contenido === false) { //si no hay contenido agrego el objeto al array
                array_push($array, $std);

            } else { //si existe contenido lo desearializo
                $buffer = json_decode($contenido);
                if(isset($buffer))
                {
                    array_push($buffer,$std);
                    $array=[...$buffer];
                }else
                {
                    array_push($array, $std);
                }
            // var_dump($buffer);die();
            //     //mapeo el array obtenido  con Auto 
            //     $array = array_map(function ($s) {
            //         $aux = new Auto($s->patente, $s->marca, $s->color, $s->precio);
            //         return $aux;
            //     }, $buffer);
            //     // agrego la isntancia actual al array
            //     array_push($array, $this);
            //     // var_dump($array);
            }

            var_dump($array);
            //sobreescribo el archivo con el array de autos
            if (file_put_contents($path, json_encode($array, JSON_PRETTY_PRINT))) {
                $obj->exito = "true";
                $obj->mensaje = "Guardado en archivo $path";
            } else {
                $obj->exito = "false";
                $obj->mensaje = "Error guardado en archivo $path";
            }
        } catch (Exception $th) {
            $obj->exito = "false";
            $mensaje = $th->getMessage();
            $obj->mensaje = "Excepcion en guardado en archivo: $mensaje";
        }
        return json_encode($obj, JSON_PRETTY_PRINT);
    }

    public static function traerJSON(string $path): array
    {

        $array = array();
        try {
            $contenido = file_get_contents($path);
            if ($contenido !== false) {

                $buffer = json_decode($contenido);

                $array = array_map(function ($s) {
                    $aux = new Auto($s->patente, $s->marca, $s->color, $s->precio);
                    return $aux;
                }, $buffer);
            }
        } catch (Exception) {
            return null;
        }
        return $array;
    }
    public static function verificarAutoJSON(string $patente): string
    {
        $existe = false;
        $obj = new stdClass();
        if (isset($patente) && !empty($patente)) {
            // $path=__DIR__;
            // $path=str_replace("\\","/",$path);
            // $path.="/archivos/autos.json";
            $array_autos = self::traerJSON("./archivos/autos.json");

            if (isset($array_autos)) {
                foreach ($array_autos as $key => $value) {
                    if (trim($value->patente) == trim($patente)) {
                        $existe = true;
                        break;
                    }
                }
            }
        }
        if ($existe) {
            $obj->exito = true;
            $obj->mensaje = "auto con patente $patente existe";
        } else {
            $obj->exito = false;
            $obj->mensaje = "auto con patente $patente NO existe";
        }
        return json_encode($obj, JSON_PRETTY_PRINT);
    }        
}
