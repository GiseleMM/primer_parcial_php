<?php
/*

EliminarUsuario.php: Si recibe el parámetro id por POST, más el parámetro accion con valor "borrar", se
deberá borrar el usuario (invocando al método Eliminar).
Retornar un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido.
*/
require_once "./clases/usuario.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_REQUEST["id"]) ? trim($_REQUEST["id"]) : null;

    $accion = isset($_REQUEST["accion"]) ? strtolower(trim($_REQUEST["accion"])) : null;
    if (isset($id, $accion) && $accion == "borrar") {
        if (Usuario::Eliminar((int)$id)) {
            echo '{"exito":true,"mensaje":"eliminacion exitosa}';
        } else {

            echo '{"exito":false,"mensaje":"NO se pudo eliminar}';
        }
    }
}
