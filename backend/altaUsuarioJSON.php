<?php
require_once "./clases/usuario.php";
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $correo=isset($_POST["correo"])?trim($_POST["correo"]):null;
    
    $clave=isset($_POST["clave"])?trim($_POST["clave"]):null;
    
    $nombre=isset($_POST["nombre"])?trim($_POST["nombre"]):null;
    if(isset($correo,$clave,$nombre))
    {
        $usuario=new Usuario(-1,$nombre,$correo,$clave,-1,"");
        echo $usuario->GuardarEnArchivo();
    }

}
?>