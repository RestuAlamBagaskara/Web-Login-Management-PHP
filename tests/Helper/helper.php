<?php

    namespace Alambagaskara\LoginManagement\App {
        function header(string $value){
            echo $value;
        }
    }

    namespace Alambagaskara\LoginManagement\Service {
        function setcookie(string $name, string $value){
            echo "$name : $value";
        }    
    }