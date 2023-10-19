<?php

/**listadoAutosBD.php: (GET) Se mostrará el listado completo de los autos (obtenidos de la base de datos) en una
tabla (HTML con cabecera). Invocar al método traer. */

require_once "./clases/autoBD.php";

use Medina\AutoBD;
//var_dump($_SERVER);
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $array = AutoBD::traer();
    //echo "estoy en listado";
   // var_dump($array);
    //  die();
    $aux = [];
    foreach ($array as $key => $value) {
        if (isset($value)) {

            $json = trim($value->toJSON());
            $obj = json_decode($json);
            array_push($aux, $obj);
        }
    }
    if (isset($_GET["tabla"]) &&  trim($_GET["tabla"]) == "mostrar") {
        echo grilla($aux);
    } else {

        echo json_encode($aux, JSON_PRETTY_PRINT);
    }
}
function grilla($array)
{


    $tabla ="<table style='border: 3px double black ;background-color:pink; width:100%'>
        <thead>
            <tr>";

    $tabla .= "<th>PATENTE</th>";
    $tabla .= "<th>MARCA </th>";
    $tabla .= "<th>COLOR  </th>";
    $tabla .= "<th>PRECIO </th>";
    $tabla .= "<th>FOTO </th>";
    $tabla .= "</tr>
        </thead>
        <tbody>";
    foreach ($array as $key => $value) {
        $tabla .= "<tr><td> $value->patente </td>";
        $tabla .= "<td> $value->marca </td>";
        $tabla .= "<td> $value->color </td>";
        $tabla .= "<td> $value->precio </td>";
        if (isset($value->pathFoto) && !empty($value->pathFoto)) {
            $foto=explode("/",$value->pathFoto);
            $tabla .= '<td><img src="./autos/fotos/'.trim(end($foto)).'" alt="foto empleado" srcset="" width="100px" height="100px"> </td>';
        } else {
            $tabla .= '<td><img src="#" alt="foto empleado" srcset="" width="100px" height="100px"> </td>';
        }
        $tabla .= "</tr>";
    }



    $tabla .= "</tbody>
            </table>";

    return $tabla;
}
