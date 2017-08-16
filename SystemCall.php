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
    
    use \Coroutine\Task;
    use \Coroutine\Scheduler;
    
    class SystemCall {
        
        /**
         * $callback
         * The callback of the caller.
         * 
         */
        protected $callback;
        
        /**
         * $methods
         * The methods which add by users.
         * 
         */
        protected static $methods = array();
        
        
        /**
         * function __construct()
         * The constructor.
         * 
         * @param $callback The callback of the caller.
         * 
         * @return null
         * 
         */
        public function __construct(callable $callback){
            
            $this->callback = $callback;
            
        }
        
        /**
         * function __invoke()
         * The caller to call the SystemCall.
         * 
         * @param Task $task
         * @param Scheduler $Scheduler
         * 
         * @return The result of the Callback.
         * 
         */
        public function __invoke(Task $task, Scheduler $Scheduler){
            
            $callback = $this->callback;
            
            return $callback($task, $Scheduler);
            
        }
        
        /**
         * static function __callStatic()
         * Handler for Users' methods.
         * 
         * @param String $name The method name.
         * @param Mixed[Array] $arg The Argruments.
         * 
         * @return Mixed
         * 
         */
        public static function __callStatic($name, $arg){
            
            //Proccess the argrument into String.
            $arg = implode(", ", $arg);
            
            //See If $name exists.
            if(!isset(self::$methods[$name])){
                throw new \Exception("Call to undefined function on SystemCall.");
                return false;
            }
            
            //Get the function.
            $func = self::$methods[$name];
            
            //Call the function.
            return $func($arg);
            
        }
        
        /**
         * static function assign()
         * Assign a processer to a Method.
         * 
         * @param String $name The method name.
         * @param Callable $callback The processer.
         * 
         * @return bool
         * 
         */
        public static function assign($name, callable $callback){
            
            return self::$methods[$name] = $callback;
            
        }
        
        ///### -*- The SystemCall functions -*- ###///
        /**
         * static function getTaskId()
         * 
         * @return The Id of the Task.
         * 
         */
        public static function getTaskId(){
            return new SystemCall(function (Task $Task, Scheduler $Scheduler){
                //Send the taskId to yield.
                $Task->setSendValue($Task->getTaskId());
                //Re-active the Task.
                $Scheduler->schedule($Task);
            });
        }
        
        /**
         * static function killTask()
         * 
         * @param $taskId The id of the task.
         * 
         * @return bool
         * 
         */
        public static function killTask($taskId){
            return new SystemCall(function (Task $Task, Scheduler $Scheduler) use ($taskId){
                //Send the status to yield.
                $Task->setSendValue($Scheduler->killTask($taskId));
                //Re-active the Task.
                $Scheduler->schedule($Task);
            });
        }
        
        /**
         * static function newTask()
         * 
         * @param Generator $Generator The orgin function.
         * 
         * @return bool
         * 
         */
        public static function newTask(\Generator $Generator){
            return new SystemCall(function (Task $Task, Scheduler $Scheduler) use ($Generator){
                //Send the task to yield.
                $Task->setSendValue($Scheduler->newTask($Generator));
                //Re-active the Task.
                $Scheduler->schedule($Task);
            });
        }
        
    }