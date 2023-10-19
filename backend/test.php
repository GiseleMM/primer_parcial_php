<?php

use Medina\AutoBD;
require_once "./clases/autoBD.php";
$auto=new AutoBD("aaaa","bbbb","rojo",1000,".\\AA888CC.012512.jpg");
$auto->guardarEnArchivo();

?>