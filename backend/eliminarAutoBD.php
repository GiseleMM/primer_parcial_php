<?php
/*
eliminarAutoBD.php: Recibe el parámetro auto_json (patente, marca, color y precio, en formato de cadena JSON)
por POST y se deberá borrar el auto (invocando al método eliminar).
Si se pudo borrar en la base de datos, invocar al método guardarJSON y pasarle cómo parámetro el valor
'./archivos/autos_eliminados.json'.
Retornar un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido.
*/

use Medina\AutoBD;

require_once "./clases/autoBD.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $auto_json = isset($_POST["auto_json"]) ? trim($_POST["auto_json"]) : null;
    if (isset($auto_json)) {

        $std = json_decode($auto_json);
       // var_dump($std);
        if (isset($std)) {

            if (AutoBD::eliminar($std->patente)) {

                $auto = new AutoBD($std->patente, $std->marca, $std->color, (float)$std->precio, (isset($std->foto) && trim($std->foto)!=="") ? $std->foto : "");
                echo $auto->guardarJSON("./archivos/autos_eliminados.json");
            }
        }
    } else {

        $rta = new StdClass();
        $rta->exito = false;
        $rta->mensaje = "ERROR eliminacion id no valido";
        echo json_encode($rta);
    }
}
