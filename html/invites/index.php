<?php
// Incluir información de la base de datos
include("conexion.php");

// Se toma el objeto json y se convierte en un arreglo "$data"
$data = json_decode(file_get_contents('php://input'), true);

// Tomar la información de quien invita
$invita = $data["Invita"];

// Tomar la información del evento
$evento = $data["Evento"];

// Tomar la información de los invitados
$invitados = $data["Invitados"];

//Cantidad de usuarios a los cuales enviar el correo
$total = count($invitados);

//Arreglo para retornar listado de usuarios con correo enviado
$listausuarios= array();

//Ciclo del tamaño de la lista de usuarios
for ($i = 0; $i < $total; $i++) {

  //Datos del invitado actual
  $invitado = $invitados[$i];

  // Destinatario
  $para  = $invitado["Email"];

  // Título del correo
  $título = 'Invitación al evento '.$evento["Name"];

  // Formateo del Mensaje en HTML
  $mensaje = '
  <html>
  <head>
    <title>Invitación al evento <strong>'. $evento["Name"] .'</strong></title>
  </head>
  <body>
    <p>Hola '.$invitado["Name"].' '.$invitado["Lastname"].'</p>
    El usuario <strong>'. $invita["Email"] .'</strong> te ha invitado al evento <strong>'. $evento["Name"] .' </strong> que estaremos realizando el '. $evento["Date"] .' a las '. $evento["Hour"] .' horas en el lugar: <strong>'. $evento["Place"] .' </strong><br><br>
    ¡Te esperamos! 
  </body>
  </html>
  ';

  // Para enviar un correo HTML, debe establecerse la cabecera Content-type
  $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
  $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

  // Cabeceras adicionales
  $cabeceras .= 'To: <'.$invitado["Email"].'>' . "\r\n";
  $cabeceras .= 'From: Invitacion <invitaciones@mapapp.com>' . "\r\n";

  // Enviar el email al usuario actual
  // Si el correo puede ser enviado, se guardará en base de datos
  if (mail($para, $título, $mensaje, $cabeceras))
  {
    //Formato de usuario para agregar al arreglo de enviados
    $enviado = array("ID_event" => $evento["ID"], "ID_user" => $invitado["ID"] , "Email" => $invitado["Email"]);

    // Fijando las variables para enviar en la query a mysql
    $id_evento = $evento["ID"];
    $id_invita = $invita["ID"];
    $id_invitado = $invitado["ID"];

    // Conexion a la BD
    try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $query = "INSERT INTO `invitaciones` (`event_id`, `host_id`, `receiver_id`) VALUES ('$id_evento' , '$id_invita' , '$id_invitado')";
      // use exec() because no results are returned
      $conn->exec($query);
      //echo "Se agregó la invitación a ".$invitado["Correo"]."<br>";
      //Se agrega el usuario al arreglo de enviados
      array_push($listausuarios, $enviado);
    }
    // Informe si ocurre una excepción
    catch(PDOException $e)
        {
        echo $query . "<br>" . $e->getMessage();
        }
    // Limpia la conexión
    $conn = null;
  }

}
//Se retorna el arreglo de usuarios a los que se envío correo en formato JSON.
echo json_encode($listausuarios);   
?>