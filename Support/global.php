<?php 

function env($key, $default = false){
    $value = getenv(strtoupper($key)); 
    return  ($value)? $value: $default; 
}