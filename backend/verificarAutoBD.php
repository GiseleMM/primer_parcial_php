<?php

use Medina\AutoBD;

require_once "./clases/autoBD.php";
/*
verificarAutoBD.php: Se recibe por POST el parámetro obj_auto, que será una cadena JSON (patente), si coincide
con algún registro de la base de datos (invocar al método traer) retornará los datos del objeto (invocar al toJSON).
Caso contrario, un JSON vacío ({}).
   */

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $obj_auto = isset($_POST["obj_auto"]) ? trim($_POST["obj_auto"]) : null;
    if (isset($obj_auto)) {
        $std = json_decode($obj_auto);
        $auto = new AutoBD($std->patente, "", "", 0, "");

        $array_autosBd = AutoBD::traer();
        if ($auto->existe($array_autosBd)) {

            $array = (array_filter($array_autosBd, fn ($item) => $item->patente === $auto->patente));
            if (count($array) == 1) {
                echo json_encode($array[0]);
            }
        } else {
            $obj = new stdClass();
            echo json_encode($obj);
        }
    }
}
