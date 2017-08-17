<?php
    /**
     * XLibAutoloader
     * 
     * This file is a part of XCoroutine.
     * Use it under the license.
     * 
     * @package XCoroutine
     * @license MIT
     * 
     */
    
    namespace Coroutine;
    
    class Autoload {
        
        public static function load($name){
            
            $name = str_replace("\\","/",$name);
            $name = str_replace("Coroutine/","",$name);
            
            if(is_file(__DIR__.'/'.$name.'.php')){
                require_once(__DIR__.'/'.$name.'.php');
            }
            
        }
        
    }
    
    spl_autoload_register("\\Coroutine\\Autoload::load");