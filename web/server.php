<?php
    if ($_SERVER["REQUEST_METHOD"] != "POST") return;
    header("Content-Type: application/json");
    require_once "system.class.php";
    $system = new System;
    $system->setRootDir("/mnt/c/laragon/www/cronjobs-converter/csv-convert");
    $system->setCompilerName("main");

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