<?php 
/*

agregarAutoBD.php: Se recibirán por POST los valores: patente, marca, color, precio y la foto para registrar un
auto en la base de datos.
Verificar la previa existencia del auto invocando al método existe. Se le pasará como parámetro el array que
retorna el método traer.
Si el auto ya existe en la base de datos, se retornará un mensaje que indique lo acontecido.
Si el auto no existe, se invocará al método agregar. La imagen se guardará en “./autos/imagenes/”, con el nombre
formado por la patente punto hora, minutos y segundos del alta (Ejemplo: AYF714.105905.jpg).
Se retornará un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido.
 */

use Medina\AutoBD;

require_once "./clases/autoBD.php";

if($_SERVER["REQUEST_METHOD"]=="POST")
{
    if($_POST)
    {
       $obj=new  stdClass();
        $patente=isset($_POST["patente"])?trim($_POST["patente"]):null;
        $marca=isset($_POST["marca"])?trim($_POST["marca"]):null;
        $color=isset($_POST["color"])?trim($_POST["color"]):null;
        $precio=isset($_POST["precio"])?trim($_POST["precio"]):null;
      $array_autosBd=AutoBD::traer();
      $buffer=new AutoBD($patente,"","",0,"");
      if($buffer->existe($array_autosBd))
      {
        $obj->exito=false;

        $obj->mensaje="patente existente en base de datos";
      }else
      {

        $extension=pathinfo($_FILES["foto"]["name"],PATHINFO_EXTENSION);
        $foto=$patente.".".date("Hms").".".$extension;
        $auto=new AutoBD($patente,$marca,$color,$precio,$foto);
        $auto->set_foto();
         if($auto->agregar())
         {
            $obj->exito=true;
            $obj->mensaje="auto agregado a base de  datos";

         }else{
            $obj->exito=false;
            $obj->mensaje="auto NO agregado a base de  datos";

         }
      }
        
  echo json_encode($obj,JSON_PRETTY_PRINT);
    
        
     
    }


}
?>