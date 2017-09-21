<?php 
  // Incluimos las credenciales para conexión
  include 'config.php';

  // Se crea la clase de conexión y ejecución de consultas
  class db_model {

    // Variable de conexion
    public $conn;

    // La función constructora crea y abre la conexión
    function __construct() {
      $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    }

    // Funcion para obtener un array de resultados
    // Solo se usara para las consultas de tipo SELECT
    function get_query($sql) {
      // Lee la cadena SQL recibida y ejecuta la consulta
      $result = $this->conn->query($sql);

      // Hace el rrecorrido por el array de datos y lo guarda en la variable $rows
      while ($rows[] = $result->fetch_assoc());
      // Cierra la consulta
      $result->close();
      // Retorna el resultado obtenido
      return $rows;
    }

    // Funcion para hacer cambios dentro de la base de datos
    // Solo se usara para las consultas de tipo INSERT
    function set_query($sql) {
      // Lee la cadena SQL recibida y ejecuta la consulta
      $result = $this->conn->query($sql);
      // Retorna el resultado
      return $result;

    }

    // Se cierra la conexión previamente abierta.
    function __destruct() {
      $this->conn->close();
    }
  }
?>