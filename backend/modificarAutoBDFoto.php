<?php

/**modificarAutoBDFoto.php: Se recibirán por POST los siguientes valores: auto_json (patente, marca, color y
precio, en formato de cadena JSON) y la foto (para modificar un auto en la base de datos). Invocar al método
modificar.
Si se pudo modificar en la base de datos, la foto original del registro modificado se moverá al subdirectorio
“./autosModificados/”, con el nombre formado por la patente punto 'modificado' punto hora, minutos y segundos
de la modificación (Ejemplo: AYF714.renault.modificado.105905.jpg).
Se retornará un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido.
Si se invoca por GET (sin parámetros), se mostrarán en una tabla (HTML) la información de todos los autos
modificados y sus respectivas imágenes.

Parte 4 (hasta un 10)
En el directorio raíz del proyecto, agregar la siguiente página:
 */

use Medina\AutoBD;

require_once "./clases/autoBD.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $auto_json = isset($_POST["auto_json"]) ? trim($_POST["auto_json"]) : null;
    if (isset($auto_json)) {
        $std = json_decode($auto_json);
        $autoBd = new AutoBD($std->patente, $std->marca, $std->color, $std->precio, "");
       


        $obj = new stdClass();
        $obj->exito = false;
        $obj->mensaje = "modificacion fail";
        $file = __DIR__;
        $file = str_replace("\\", "/", $file);


        $archivo = scandir("./autos/fotos/");
        $mover = "";
        var_dump($archivo);
        foreach ($archivo as $key => $value) {
            if (trim($value) !== "") {
                if (str_starts_with(trim($value), $std->patente)) {
                    $mover = trim($value);
                    break;
                }
            }
        }
       // echo $mover;
        if (trim($mover) !== "") {
            echo "ENTRE";
            $array = explode(".", $mover);
            $autoBd->set_foto();
            $destino = "./archivos/autosModificados/$std->patente.$std->marca.modificado." . date("Hms") . "." . end($array);
            if ($autoBd->Modificar()) {
                if (copy("./autos/fotos/$mover", $destino)) {
                    unlink("./autos/fotos/$mover");
                    $obj->exito = true;
                    $obj->mensaje = "modificacion exitosa";
                }
            }
        } else {
            $autoBd->set_foto();
            if ($autoBd->Modificar())
            {

                $obj->exito = true;
                $obj->mensaje = "modificacion exitosa";
            }
        }
    }


    echo json_encode($obj, JSON_PRETTY_PRINT);
}
if($_SERVER["REQUEST_METHOD"]=="GET")
{
    $array=array();
    $archivos=scandir("./archivos/autosModificados/");
    foreach ($archivos as $key => $value) {
        if((trim($value)!=="" )&& str_ends_with($value,".jpg"))
        {
            $buffer=explode(".",$value);
            $std=new stdClass();
            $std->patente=$buffer[0];
            $std->marca=$buffer[1];
            $std->hora=$buffer[3];
            $std->foto=$value;
            array_push($array,$std);

        }
        
    }
    echo grilla($array);
}
function grilla($array){

   
    $tabla="<table style='border: 1px solid blue ;background-color:aquamarine; width:50px height:50px '>
    <thead>
        <tr>";
    
            $tabla.= "<th>PATENTE </th>";
            
            $tabla.= "<th>MARCA</th>";
            
            $tabla.= "<th>HORA </th>";
            
            $tabla.= "<th>FOTO  </th>";
            
       
    
        $tabla.="
        </tr>
    </thead>
    <tbody>";
        foreach ($array as $key => $value) {
        $tabla.="<tr><td> $value->patente </td>";
        $tabla.="<td> $value->marca </td>";
        $tabla.="<td> $value->hora </td>";

        $tabla.='<td><img src="./archivos/autosModificados/'.$value->foto.'" alt="foto antigua">  </td>';
        $tabla.="</tr>";
        }
$tabla.="</tbody>
        </table>";

return $tabla;
}
