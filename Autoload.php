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
        
        public function load($name){
            
            $name = str_replace("\\","/",$name);
            
            if(is_file(__DIR__.'/'.$name.'.php')){
                require_once(is_file(__DIR__.'/'.$name.'.php'));
            }
            
        }
        
    }
    
    spl_autoload_register("\\Coroutine\\Autoload::load");