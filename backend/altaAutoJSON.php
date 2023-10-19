<?php

use Medina\Auto;

require_once "./clases/auto.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $marca = isset($_POST["marca"]) ? trim($_POST["marca"]) : null;
    $color = isset($_POST["color"]) ? trim($_POST["color"]) : null;
    $patente = isset($_POST["patente"]) ? trim($_POST["patente"]) : null;
    $precio = isset($_POST["precio"]) ? trim($_POST["precio"]) : null;
   

    $usuario = new Auto($patente, $marca, $color, (float)$precio);
    $path = __DIR__;
    $path = str_replace("\\", "/", $path);
    $path .= "/archivos/autos.json";
    //var_dump($path);die();
    echo $usuario->guardarJSON($path);
}
