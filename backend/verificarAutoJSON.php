<?php

use Medina\Auto;

require_once "./clases/auto.php";

if($_SERVER["REQUEST_METHOD"]=="POST")
{
    $patente=isset($_POST["patente"])?trim($_POST["patente"]):null;
    if(isset($patente))
    {

      echo Auto::verificarAutoJSON($patente);
  
    }
}
?>