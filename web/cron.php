<?php
    require_once "system.class.php";
    $system = new System;
    $system->setRootDir("/mnt/c/laragon/www/cronjobs-converter/csv-convert");
    $system->setCompilerName("main");
    $system->startCompiler();