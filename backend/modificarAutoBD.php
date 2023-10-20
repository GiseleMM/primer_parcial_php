<?php

/**modificarAutoBD.php: Se recibirán por POST los siguientes valores: auto_json (patente, marca, color y precio, en
formato de cadena JSON) para modificar un auto en la base de datos. Invocar al método modificar.*/

use Medina\AutoBD;

require_once "./clases/autoBD.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $rta = new stdClass();
    $rta->exito = false;
    $rta->mensaje = "Error en modificacion";
    $auto_json = isset($_POST["auto_json"]) ? trim($_POST["auto_json"]) : null;
    $std = json_decode($auto_json);
    //var_dump($std);
    $auto_modificar = new AutoBD($std->patente, $std->marca, $std->color, (float)$std->precio);
    //var_dump($auto_modificar);
    //$empl_modificar->set_foto();
    if ($auto_modificar->modificar()) {
        $rta->exito = true;
        $rta->mensaje = "modificacion exitosa en base de datos";
    }
    echo json_encode($rta, JSON_PRETTY_PRINT);
}
