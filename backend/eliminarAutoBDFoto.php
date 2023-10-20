<?php
/*
eliminarAutoBDFoto.php: Se recibe el parámetro auto_json (patente, marca, color, precio y pathFoto en formato
de cadena JSON) por POST. Se deberá borrar el auto (invocando al método eliminar).
Si se pudo borrar en la base de datos, invocar al método guardarEnArchivo.
Retornar un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido.
Si se invoca por GET (sin parámetros), se mostrarán en una tabla (HTML) la información de todos los autos
borrados y sus respectivas imágenes.
*/

use Medina\AutoBD;

require_once "./clases/autoBD.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {


    // var_dump($_POST);
    $auto_json = isset($_POST["auto_json"]) ? trim($_POST["auto_json"]) : null;
    var_dump($auto_json);
    $rta = new stdClass();
    $rta->exito = false;
    $rta->mensaje = "error en eliminacion de auto";
    if (isset($auto_json)) {

        $std = json_decode($auto_json);
        var_dump($std);
        if (isset($std)) {
            //echo "entre a std";

            if (AutoBD::eliminar($std->patente)) {

                $foto = isset($std->pathFoto) ? trim($std->pathFoto) : null;
                if (isset($foto)) {
                    $array_foto = explode("/", $foto);
                    //$auto = new AutoBD($std->patente, $std->marca, $std->color, (float)$std->precio, $std->pathFoto ? $std->pathFoto : "");
                    $auto = new AutoBD($std->patente, $std->marca, $std->color, (float)$std->precio, end($array_foto));
                    echo  $auto->guardarEnArchivo();
                }
            }
        }
    } else {

        echo json_encode($rta, JSON_PRETTY_PRINT);
    }
}
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $contenido = file_get_contents("./archivos/autosbd_borrados.txt");
    //var_dump(json_decode($contenido));
    echo grilla(json_decode($contenido));
}
function grilla($array)
{

    $tabla = "<table style='border: 1px solid blue ;background-color:aquamarine'>
    <thead>
        <tr>";

    $tabla .= "<th>PATENTE</th>";

    $tabla .= "<th>MARCA </th>";

    $tabla .= "<th>COLOR  </th>";

    $tabla .= "<th>PRECIO </th>";

    $tabla .= "<th>FOTO </th>";

    $tabla .= "
        </tr>
    </thead>
    <tbody>";
    foreach ($array as $key => $value) {
        $tabla .= "<tr><td> $value->patente </td>";
        $tabla .= "<td> $value->marca </td>";
        $tabla .= "<td> $value->color </td>";
        $tabla .= "<td> $value->precio </td>";
        if (isset($value->pathFoto) && !empty($value->pathFoto)) {
            $tabla .= '<td><img src="./archivos/autosBorrados/' . $value->pathFoto . '" alt="foto empleado" srcset="" width="100px" height="100px"> </td>';
        } else {
            $tabla .= '<td><img src="#" alt="foto empleado" srcset="" width="100px" height="100px"> </td>';
        }
        $tabla .= "</tr>";
    }



    $tabla .= "</tbody>
        </table>";

    return $tabla;
}
