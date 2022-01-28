<?php
    require_once "settings.php";
    
    if ($_SERVER["REQUEST_METHOD"] != "POST") return;
    header("Content-Type: application/json");

    $data = json_decode(file_get_contents("php://input"));
    $action = $data->action ?? false;

    if ($action){
        switch($action){
            case "GetProcessStatuss":
                $system->getStatuss();
                break;
            case "StartCompiler":
                $system->startCompiler();
                break;
            case "killCompiler":
                $system->killCompiler();
                break;
            }
    }