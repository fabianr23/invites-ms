<?php
 header("Access-Control-Allow-Origin: *");
    //http://stackoverflow.com/questions/15485354/angular-http-post-to-php-and-undefined
    $postdata = file_get_contents("php://input");
    
    if (isset($postdata)) {
        $request = json_decode($postdata);
        $username = $request->username;
 
        if ($username != "") {
            echo "Server returns: " . $username;
        }
        else {
            echo "Empty username parameter!";
        }
    }
    else {
        echo "Not called properly with username parameter!";
    }
?>