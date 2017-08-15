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
    use \Coroutine\SystemCall;
    
    class Scheduler {
        
        /**
         * $currentTaskId
         * The current max task Id.
         * 
         */
        protected $currentTaskId = 0;
        
        /**
         * $taskMap
         * The map of the tasks.
         * 
         */
        protected $taskMap = array();
        
        /**
         * $taskQueue
         * The Queue to execute the Tasks.
         * 
         */
        protected $taskQueue;
        
        /**
         * function __construct()
         * The constructor.
         * 
         * @return null
         * 
         */
        public function __construct() {
            
            //Init the Queue.
            $this->taskQueue = new \SplQueue();
            
        }
        
        /**
         * function newTask()
         * Create a new task.
         * 
         * @param Generator $Generator The orgin function.
         * 
         * @return The taskId.
         * 
         */
        public function newTask(\Generator $Generator){
            
            //Get the TaskId.
            $tid = ++$this->currentTaskId;
            
            //Create a Task.
            $task = new Task($Generator, $tid);
            
            //Store the Task.
            $this->taskMap[$tid] = $task;
            
            //Add the task into the Queue.
            $this->schedule($task);
            
            return $tid;
            
        }
        
        /**
         * function schedule()
         * Add a Task to the Queue.
         * 
         * @param Task $task The task.
         * 
         * @return null
         * 
         */
        public function schedule(Task $task){
            
            $this->taskQueue->enqueue($task);
            
        }
        
        /**
         * function run()
         * Run the Tasks.
         * 
         * @return null;
         * 
         */
        public function run(){
            
            //While the Queue is not empty
            while(!$this->taskQueue->isEmpty()){
                
                //Get the Task from the Queue.
                $task = $this->taskQueue->dequeue();
                
                //Run the Task.
                $return = $task->run();
                
                //Proccess the SystemCall
                if ($return instanceof SystemCall) {
                    
                    //Run the SystemCall.
                    $return($task, $this);
                    
                    //continue.
                    continue;
                    
                }
                
                //Is the Task finished
                if($task->isFinished()){
                    
                    //Get the taskId.
                    $tid = $task->getTaskId();
                    
                    //Remove the task.
                    unset($this->taskMap[$tid]);
                    
                } else {
                    
                    //Re-run the task
                    $this->schedule($task);
                    
                }
                
            }
            
        }
        
        /**
         * function killTask()
         * Kill a task.
         * 
         * @param $tid The id of the task.
         * 
         * @return bool
         * 
         */
        public function killTask($tid){
            
            //Is Task Avalible
            if (!isset($this->taskMap[$tid])) {
                return false;
            }
            
            //Unset in the map.
            unset($this->taskMap[$tid]);
            
            //Unset in the queue.
            foreach ($this->taskQueue as $i => $task) {
                if ($task->getTaskId() === $tid) {
                    unset($this->taskQueue[$i]);
                    break;
                }
            }
            
            return true;
            
        }
        
    }