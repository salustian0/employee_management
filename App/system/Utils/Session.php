<?php
namespace App\system\Utils;

class Session{

    static function setFlashData($name, $value){
        if(!empty($name) && !empty($value)) $_SESSION['flash_data'][$name] = $value;
    }

    static function getFlashData($name){
        $flashData = $_SESSION['flash_data'][$name] ?? [];
        if(!empty($flashData)){
            unset($_SESSION['flash_data'][$name]);
        }
        return $flashData;
    }
    static function getAllFlashData(){
        $flashData = $_SESSION['flash_data'] ?? [];
        unset($_SESSION['flash_data']);
        return $flashData;
    }
}