<?php
    /**
     * PHP Coroutine
     * 
     * This file is a part of XCoroutine.
     * Licensed under MIT.               
     * 
     * @author xtl<xtl@xtlsoft.top>
     * @license MIT
     * @package XCoroutine
     * 
     * Some code are from laruence's blog.
     * 
     */
    
    namespace Coroutine;
    
    class Task {
        
        /**
         * $taskId 
         * The ID of the task.
         * 
         */
        protected $taskId;
        
        /**
         * $Generator
         * The Orgin Generator.
         * 
         */
        protected $Generator;
        
        /**
         * $sendValue
         * The value to send.
         * 
         */
        protected $sendValue;
        
        /**
         * $beforeFirstYield
         * Make sure the first value could be returned.
         * 
         */
        protected $beforeFirstYield = true;
        
        /**
         * function __construct()
         * The constructor.
         * 
         * @param Generator $Generator The Coroutine Orgin function.
         * @param int $taskId The id of the Task.
         * 
         * @return null
         * 
         */
        public function __construct(\Generator $Generator, $taskId=-1){
            
            $this->taskId = $taskId;
            $this->Generator = $Generator;
            
        }
        
        /**
         * function getTaskId()
         * The method to get the id of the task.
         * 
         * @return The id of the task.
         * 
         */
        public function getTaskId(){
            
            return $this->taskId;
            
        }
        
        /**
         * function setSendValue()
         * The method to set the value to send.
         * 
         * @param $value The value to send.
         * 
         * @return null
         * 
         */
        public function setSendValue($value){
            
            $this->sendValue = $value;
            
        }
        
        /**
         * function run()
         * Run the task.
         * 
         * @param Not-Required Mixed $sendValue
         * 
         * @return The return value of the task.
         * 
         */
        public function run($sendValue=null){
            
            if($sendValue!==null) $this->sendValue = $sendValue;
            
            //Compatible the first item.
            
            if($this->beforeFirstYield){
                
                //Make the flag false.
                $this->beforeFirstYield = false;
                
                //Return the current return value.
                return $this->Generator->current();
                
            }else{
                
                /**
                 * Var $return
                 * The return value.
                 * 
                 */
                $return = $this->Generator->send($this->sendValue);
                
                //Clear the sendValue
                $this->sendValue = null;
                
                //Return the Return value
                return $return;
                
            }
            
        }
        
        /**
         * function isFinished()
         * 
         * @return bool Is the task Finished.
         * 
         */
        public function isFinished(){
            
            return !$this->Generator->valid();
            
        }
        
    }