<?php 
// Se incluye el archivo de conexion de base de datos
include 'core/db_model.php';
// Se incluye la interfaz de Modelo
include 'core/iModel.php';

// Se crea la clase que ejecuta el llamado a las funciones
class generic_class extends db_model implements iModel {
  // Identifica con que tabla se trabaja
  public $entity;
  // Información que sera enviada a la Base de datos
  public $data;
  
  // Esta función se activará al utilizar el método GET
  // Por defecto el parámetro Id será 0 hasta que se modifique
  function get($id = 0) {
    //Si Id es igual a 0, se solicitaran todos los elementos. 
    if($id == 0) {
        return $this->get_query(sprintf("SELECT * FROM %s", $this->entity));
    } 
    else {
        // Si existe un id, se solicitará el elemento que cumpla con ese id
        return $this->get_query(sprintf("SELECT * FROM %s WHERE event_id = %d", $this->entity, $id));
    }
  }

  // Esta funcion sera llamada al momento de usar el metodo POST
  function post() {

    return $this->set_query(
            sprintf("INSERT INTO %s %s", $this->entity, $this->data)
        );
    }
}
?>