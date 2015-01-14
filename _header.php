<?php
    //Kludge to be able to load libraries both in Pluton and in stand-alone tool version.
    $tool_directory = getcwd() . '/' . str_replace('-', '/', $document->topic);
    ini_set('include_path', ini_get('include_path') . ":$tool_directory");
