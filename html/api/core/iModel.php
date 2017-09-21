<?php 
 // Define cada una de las funciones que el model.php debe especificar
 interface iModel
 {
     // GET : Solicitar un elemento
     public function get();
     // POST : Publicar un nuevo elemento
     public function post();
 }
?>