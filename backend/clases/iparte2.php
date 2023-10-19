<?php


namespace Medina;
interface IParte2
{
    /**Crear, en ./clases, la interface IParte2. Esta interface poseerá los métodos:
● eliminar: este método estático, elimina de la base de datos el registro coincidente con la patente recibida
cómo parámetro. Retorna true, si se pudo eliminar, false, caso contrario.
● modificar: Modifica en la base de datos el registro coincidente con la instancia actual (comparar por
patente). Retorna true, si se pudo modificar, false, caso contrario.
Implementar la interfaz en la clase AutoBD. */

  
    public function modificar():bool;
    public static function eliminar($pantente):bool;

}

?>