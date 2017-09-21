<?php
  echo 'Recibido... <br>';

  // Incluir información de la base de datos
  include("conexion.php");

  print_r ($_GET);

  $userid = ($_GET["userid"]);

  if ($userid){
    // Conexion a la BD
    $conn = new mysqli($servername,$username,$password,$dbname);

    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT * FROM `invitaciones` WHERE `invitaciones`.`correo_invita` = $userid ";
    $result = $conn->query($query);
    $rows = array();

    if ($result->num_rows > 0){
      while($row = $result->fetch_assoc()) {
          $rows[] = $row;
      }
      echo json_encode($rows);  
    }
    else{
      echo "No results";
    }
  }  
  else{
    // Obtener el código de la respuesta actual y establecer uno nuevo
    http_response_code(400);

    // Obtener el nuevo código de respuesta
    return var_dump(http_response_code());
  }

  // Enviar el email al usuario actual
  // Si el correo puede ser enviado, se guardará en base de datos
  if (mail($para, $título, $mensaje, $cabeceras))
  {

    // Conexion a la BD
    try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $query = "INSERT INTO `invitaciones` (`id_evento`, `correo_invita`, `correo`) VALUES ('$id_evento' , '$correo_invita' , '$correo_invitado')";
      // use exec() because no results are returned
      $conn->exec($query);
      echo "Se agregó la invitación a ".$invitado["Correo"]."<br>";
    }
    // Informe si ocurre una excepción
    catch(PDOException $e)
        {
        echo $query . "<br>" . $e->getMessage();
        }
    // Limpia la conexión
    $conn = null;
    }
    // Informe si el correo no pudo ser enviado
  else{
    echo "el correo hacia ". $invitado["Correo"] ." no pudo ser enviado.";
  } 
?>