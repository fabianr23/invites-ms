# invites-ms

Run docker-compose build (Solo la primera vez)

Run docker-compose up

/** EndPoints **/

URL : /api/invitaciones


-------> GET /api/invitaciones

Devuelve un listado de todas las invitaciones guardadas:

{
    "statusCode": 200,
    "statusMessage": "OK",
    "data": [
        {
            "id": "35",
            "event_id": "1",
            "host_id": "1",
            "receiver_id": "2",
            "sent_date": "2017-09-20 17:36:55"
        },
        {
            "id": "36",
            "event_id": "1",
            "host_id": "1",
            "receiver_id": "3",
            "sent_date": "2017-09-20 17:36:55"
        }, 
            ...
            ...
            ...
        {
            "id": "74",
            "event_id": "23",
            "host_id": "10",
            "receiver_id": "15",
            "sent_date": "2017-09-21 00:00:01"
        }
    ]
}



--------------> GET /api/invitaciones/id

Devuelve un listado de las invitaciones que se generaron para el id del evento proporcionado:

Ej: 

/api/invitaciones/1

{
    "statusCode": 200,
    "statusMessage": "OK",
    "data": [
        {
            "id": "35",
            "event_id": "1",
            "host_id": "1",
            "receiver_id": "2",
            "sent_date": "2017-09-20 17:36:55"
        },
        {
            "id": "36",
            "event_id": "1",
            "host_id": "1",
            "receiver_id": "3",
            "sent_date": "2017-09-20 17:36:55"
        },
        {
            "id": "37",
            "event_id": "1",
            "host_id": "1",
            "receiver_id": "4",
            "sent_date": "2017-09-20 17:36:55"
        }
    ]
}



----------------> POST /api/invitaciones/

Se espera un objeto construido de la siguiente manera:

{
	"Invita":{
		"ID": 10,
		"Email": "pepito2@jmail.com"
	},
    "Evento":{
    	"ID": 23,
        "Name":"Marcha contra las marchas",
        "Date":"31/12/2017",
        "Hour":"21:00",
        "Place":"Calle 26"
    },
    "Invitados":[
        {	
        	"ID": 5,
        	"Name":"Farid Camilo", 
        	"Lastname":"Mondragon",
			"Email": "graciasfaryd2@jmail.com"        	
        },
        {
        	"ID": 10,
        	"Name":"Marina Maria", 
        	"Lastname":"Meladouñe",
			"Email": "mym2@jmail.com"  
        },
        {
        	"ID": 15,
        	"Name":"Graciela Patricia", 
        	"Lastname":"Piedras de los Ríos",
			"Email": "riosuena2@jmail.com"  
        }
    ]
 }

 -->> Este método retorna el código de estado, un mensaje de estado, y un arreglo data con la el listado de los registros que fueron agregados:

 {
    "statusCode": 200,
    "statusMessage": "OK",
    "data": [
        {
            "id": "35",
            "event_id": "1",
            "host_id": "1",
            "receiver_id": "2",
            "sent_date": "2017-09-20 17:36:55"
        },
        {
            "id": "36",
            "event_id": "1",
            "host_id": "1",
            "receiver_id": "3",
            "sent_date": "2017-09-20 17:36:55"
        },
        {
            "id": "37",
            "event_id": "1",
            "host_id": "1",
            "receiver_id": "4",
            "sent_date": "2017-09-20 17:36:55"
        }
    ]
}


/************ Manejo de errores ***************/

------------> GET /api/invitaciones/id
-> Si no existe el id :

{
    "statusCode": 404,
    "statusMessage": "Not Found",
    "data": null
}


------------> POST /api/invitaciones/id
-> Si se trata de enviar un id al método POST

{
    "statusCode": 405,
    "statusMessage": "Method Not Allowed",
    "data": null
}


------------> POST /api/invitaciones/
-> Si la lista de invitados está vacia:

Ej :

{
	"Invita":{
		"ID": 10,
		"Email": "pepito2@jmail.com"
	},
    "Evento":{
    	"ID": 23,
        "Name":"Marcha contra las marchas",
        "Date":"31/12/2017",
        "Hour":"21:00",
        "Place":"Calle 26"
    },
    "Invitados":[]
}

-> Responde:

{
    "statusCode": 201,
    "statusMessage": "Empty List",
    "data": null
}


------------> PUT - DELETE /api/invitaciones/
-> Al intentar realizar PUT o DELETE a esta url (no están permitidos)

{
    "statusCode": 405,
    "statusMessage": "Method Not Allowed!",
    "data": null
}