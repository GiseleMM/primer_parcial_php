<?php 
/*
agregarAutoSinFoto.php: Se recibe por POST el parámetro auto_json (patente, marca, color y precio), en formato
de cadena JSON. Se invocará al método agregar.
Se retornará un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido.
 */

use Medina\AutoBD;

require_once "./clases/autoBD.php";

if($_SERVER["REQUEST_METHOD"]=="POST")
{
    if($_POST)
    {
        $auto_json=isset($_POST["auto_json"])?trim($_POST["auto_json"]):null;
    
        $rta=new stdClass();
        $rta->exito=false;
        $rta->mensaje="No se pudo agregar auto a base de datos :(";
        if(isset($auto_json))
        {
            $std=json_decode($auto_json);
            $autoBd=new AutoBD($std->patente,$std->marca,$std->color,(float)$std->precio);
            if($autoBd->agregar())
            {
                $rta->exito=true;
                $rta->mensaje="Alta exitosa en base de datos";
            }
      
        }
        echo json_encode($rta,JSON_PRETTY_PRINT);
    }


}
?>