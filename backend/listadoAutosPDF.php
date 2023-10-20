<?php

/**En el directorio raíz del proyecto, agregar la siguiente página:
listadoAutosPDF.php: (GET) Generar un listado de los autos de la base de datos y mostrarlo con las siguientes
características:
● Encabezado (apellido y nombre del alumno a la izquierda y número de página a la derecha).
● Cuerpo (Título del listado, listado completo de los autos con su respectiva foto).
● Pie de página (fecha actual, centrada). */

use Medina\AutoBD;

require_once "./clases/autoBD.php";

if($_SERVER["REQUEST_METHOD"]=="GET")
{
$array=AutoBD::traer();
$array_stdClass=[];
if(isset($array)){
    foreach ($array as $key => $value) {
        if(isset($value))
        {
            $json=$value->toJSON();
            $std=json_decode($json);
            array_push($array_stdClass,$std);
        }
      
    }

    $tabla= grilla($array_stdClass);
    //seteo la zona horaria del servidor 
    date_default_timezone_set("America/Argentina/Buenos_Aires");
}
}

function grilla($array){

   
    $tabla="<table class='tablaPDF'>
    <thead>
        <tr>";
    
            $tabla.= "<th>PATENTE </th>";
            
            $tabla.= "<th>MARCA</th>";
            
            $tabla.= "<th>COLOR </th>";
            
            $tabla.= "<th>PRECIO </th>";
            
            $tabla.= "<th>FOTO </th>";
            
         
    
        $tabla.="
        </tr>
    </thead>
    <tbody>";
        foreach ($array as $key => $value) {
        $tabla.="<tr><td> $value->patente </td>";
        $tabla.="<td> $value->marca </td>";
        $tabla.="<td> $value->color </td>";
        $tabla.="<td> $value->precio </td>";
        if((isset($value) && trim($value->pathFoto)!==""))
        {
            $foto=explode("/",$value->pathFoto);
    
            $tabla.='<td><img src="./autos/fotos/'.end($foto).'" alt="foto">  </td>';
        }else
        {
            $tabla.='<td><img src="#" alt="foto antigua">  </td>';

        }
        
        $tabla.="</tr>";
        }
$tabla.="</tbody>
        </table>";

return $tabla;
}

$css=file_get_contents("./stylepdf.css");

require_once "../vendor/autoload.php";
header("Content-type:application/pdf");
$mpdf=new \Mpdf\Mpdf(['orientation' => 'P', 
'pagenumPrefix' => 'Nro. ',
'pagenumSuffix' => ' - ',
'nbpgPrefix' => ' de ',
'nbpgSuffix' => ' páginas']);
// Write some HTML code:
$mpdf->SetHeader('Gisele Medina 3A||{PAGENO}{nbpg}');
$mpdf->SetFooter('|{DATE j-m-Y}|');

$mpdf->writeHTML($css,\Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML("<h1>LISTAR PDF</h1>");
$mpdf->WriteHTML($tabla);

// Output a PDF file directly to the browser
$mpdf->Output();
?>