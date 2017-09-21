<?php 
// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");  
// Se incluye el archivo que contiene la clase generica
include 'model.php';

// Se toma la URL solicitada y se guarda en un array de datos
// Por ejemplo si la URL solicitada es http://localhost/api/usuario
// $_SERVER['REQUEST_URI'] imprime "/api/usuario"

// Esto nos ayuda a identificar cuando se esta solicitando la URL general o un elemento especifico
$array = explode("/", $_SERVER['REQUEST_URI']);

// Obtener el cuerpo de la solicitud HTTP (Peticiones POST)
$bodyRequest = file_get_contents("php://input");

// Limpia los campos en blanco de la url (útil cuando la url termina en /)
foreach ($array as $key => $value) {
    if(empty($value)) {
        unset($array[$key]);
    }
}

//Si el final de la url es mayor a 0 significa que nos solicitan un id específico
if(end($array)>0) {
// Si es valor numérico, se crea id y entity
    $id = $array[count($array)];
    $entity = $array[count($array) - 1];
} else {
// Si es un tipo string, solo crea la variable de la entidad solicitada
    $entity = $array[count($array)];
}

// Variable que guarda la instancia de la clase genérica
$obj = get_obj();

// Se pasa a la entidad el valor de la entidad actual (nombre de la tabla)
$obj->entity = $entity;

// Analiza el protocolo HTTP usado (GET o POST)
switch ($_SERVER['REQUEST_METHOD']) {
case 'GET':
// Acciones del Metodo GET
// Si la variable Id existe, solicita al modelo el elemento específico
if(isset($id)) {
    $data = $obj->get($id);
} else // Si no existe, solicita todos los elementos
    {
        $data = $obj->get();
    }

// Elimina el último elemento del array $data, ya que usualmente el último viene en NULL
array_pop($data);

// Manejo de respuestas
// Si la los elementos que trae el array de $data son 0
if(count($data)==0) {
    // Si la variable Id existe pero el array de $data no arroja resultado, significa que elemento no existe
    if(isset($id)) {
        print_json(404, "Not Found", null);
    // Pero si la variable Id existe y no trae $data, ya que no buscamos un elemento especifico, significa que la entidad no tiene elementos que mostrar
    } else {
        print_json(204, "Not Content", null);
    }
} 
else { // Si los elementos de $data son > 0
    // Imprime la informacion solicitada
    print_json(200, "OK", $data);
}
break;

// Acciones del Metodo POST
case 'POST':

/* La URL solicita por POST solo puede ser de estilo http://localhost/api/invitaciones no debería existir un ID */
if(!isset($id)) {
    // Se toma el objeto json y se convierte en un arreglo "$datospost"
    $datospost = json_decode($bodyRequest, true);

    // Tomar la información de quien invita
    $invita = $datospost["Invita"];

    // Tomar la información del evento
    $evento = $datospost["Evento"];

    // Tomar la información de los invitados
    $invitados = $datospost["Invitados"];

    //Cantidad de usuarios a los cuales enviar el correo
    $total = count($invitados);

    //Arreglo para retornar listado de usuarios con correo enviado
    $listausuarios= array();

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

            // Renderiza la información obtenida que luego será guardada en la Base de datos
            $obj->data = "(`event_id`, `host_id`, `receiver_id`) VALUES ('$id_evento' , '$id_invita' , '$id_invitado')";

            // Ejecuta la funcion post() que se encuentra en la clase genérica
            $data = $obj->post();
            if ($data) { // Si re realizó todo correctamente, añadimos el usuario a la lista de notificados
                array_push($listausuarios, $enviado);
            }
        }

    }
    // Tamaño de la lista de enviados
    $listatotal = count($listausuarios);

    // Si la lista es mayor a 0 se retorna mensaje de creación, y el listado de los correos a los cuales se les envió notificación
    if($listatotal > 0) {
        print_json(201, "Created", $listausuarios);
        break;
    }
    else{ // Si la lista está vacia, no se envió ninguna invitación
       print_json(201, "Empty List", null);
       break;
    }
} else 
    { //Si existe un ID retornamos error.
        print_json(405, "Method Not Allowed", null);
        break;
    }

default:
// Si se solicito algo diferente a POST o GET
    print_json(405, "Method Not Allowed!", null);
    break;
}

// ---------------------- Funciones controladoras ------------------------------- //

// Esta función crea la instancia de la clase genérica y la retorna
function get_obj() {
    $object = new generic_class;
    return $object;
}

// Esta funcion imprime las respuesta en estilo JSON y establece los estatus de la cebeceras HTTP
function print_json($status, $mensaje, $data) {
    header("HTTP/1.1 $status $mensaje");
    header("Content-Type: application/json; charset=UTF-8");

    $response['statusCode'] = $status;
    $response['statusMessage'] = $mensaje;
    $response['data'] = $data;

    echo json_encode($response, JSON_PRETTY_PRINT);
}
?>