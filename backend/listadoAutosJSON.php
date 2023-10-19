<?php
//ListadoUsuariosJSON.php: (GET) Se mostrará el listado de todos los usuarios en formato JSON.

use Medina\Auto;

require_once "./clases/auto.php";
$array = Auto::traerJSON("./archivos/autos.json");
if (isset($array)) {
    $aux = [];
    foreach ($array as $key => $value) {
        if (isset($value)) {
            $json=trim($value->toJSON());
            $obj=json_decode($json);
            array_push($aux, $obj);
        }
       
    }
    echo json_encode($aux, JSON_PRETTY_PRINT);
}
