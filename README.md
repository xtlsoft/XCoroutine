# XCoroutine
XCoroutine, the PHP Coroutine.
## install 安装
```sh
composer require xtlsoft/xcoroutine
```
## Intro 介绍
- Task: 任务实例。您可以向里面传入一个Generator，然后通过我们的封装对他进行操作。
- Scheduler: 多任务执行封装。您可以往里面add许多Task，然后执行。每个Task有唯一的ID，一个Task进行部分计算调用yield后，让下一个Task进行计算，以此循环，实现多任务管理。
- SystemCall: Task之间和Task与Scheduler之间传递数据、互相操作的接口。
- DynamicObject: 动态编程类，让你的类支持动态定义/销毁变量和方法。

## Usage 使用
1. 初识yield
    例子：
    ```php
    <?php
        
        foreach(range(1,10) as $i){
            echo $i." ";
        }
        echo "<br>\n";
        
        function xrange($a,$b,$c=1){
            for($i = $a; $a <= $b; $i+=$c){
                yield $i;
            }
        }
        
        foreach(xrange(1,10) as $i){
            echo $i." ";
        }
        
    ```
    输出：
    ```html
    1 2 3 4 5 6 7 8 9 10
    1 2 3 4 5 6 7 8 9 10
    ```
2. Task 使用
    例子：
    ```php
    <?php
        require "vendor/autoload.php";
        use Coroutine\Task;
        
        function TaskExample(){
            while(true){
                $text = (yield);
                echo "TaskRecv $text <br />\n";
            }
        }
        
        $Task = new Task(TaskExample());
        $Task->run();
        $Task->run("A Test Text 1");
        $Task->setSendValue("Another Test Text");
        $Task->run();
        
    ```
    输出：
    ```html
    TaskRecv A Test Text 1 
    TaskRecv Another Test Text 
    ```
3. Scheduler 使用
    例子#1：
    ```php
    <?php
        require_once "vendor/autoload.php";
        use Coroutine\Scheduler;
        
        function TaskExample($num){
            for($i=0; $i<10; ++$i){
                echo "Task$num RUN $i <br>\n";
                yield;
            }
        }
        
        $scheduler = new Scheduler();
        $scheduler->newTask(TaskExample(1));
        $scheduler->newTask(TaskExample(2));
        $scheduler->newTask(TaskExample(3));
        
        $scheduler->run();
    ```
    例子#2:
    ```php
    <?php
        require_once "vendor/autoload.php";
        use Coroutine\Scheduler;
        
        function t1(){
            for($i=0; $i<10; ++$i){
                echo "t1 RUN $i <br>\n";
                yield;
            }
        }
        function t2(){
            for($i=0; $i<10; ++$i){
                echo "t2 RUN $i <br>\n";
                yield;
            }
        }
        
        $scheduler = new Scheduler();
        $scheduler->newTask(t1());
        $scheduler->newTask(t2());
        
        $scheduler->run();
    ```
4. SystemCall 使用
    例子：
    ```php
    <?php
        require_once "vendor/autoload.php";
        use Coroutine\Scheduler;
        use Coroutine\SystemCall;
        
        function TaskExample($num){
            //获取TaskId
            $tid = (yield SystemCall::getTaskId());
            
            for($i=0; $i<10; ++$i){
                echo "Task$num $tid RUN $i <br>\n";
                if($i == 3){
                    yield SystemCall::killTask(1);
                }
                if($i == 5){
                    yield SystemCall::newTask(Task2());
                }
                yield;
            }
        }
        
        function Task2(){
            $tid = (yield SystemCall::getTaskId());
            for($i=0; $i<3; ++$i){
                echo "Another Task $tid RUN $i <br>\n";
            }
        }
        
        $scheduler = new Scheduler();
        $scheduler->newTask(TaskExample(1));
        $scheduler->newTask(TaskExample(2));
        $scheduler->newTask(TaskExample(3));
        
        $scheduler->run();
    ```
5. DynamicObject 使用
    例子：
    ```php
    <?php
        require("vendor/autoload.php");
        use Coroutine\DynamicObject;
        
        $obj = new DynamicObject;
        
        $obj->abcd = 123;
        
        $obj->myFunc = function($a) use ($obj){
            return ("\$a * abcd = ".($a * $obj->abcd)."<br />\n");
        };
        
        echo $obj->myFunc(2);
        
        unset($obj->abcd);
        
        echo $obj->myFunc(1);
        
        $obj->myFunc = function($a, $b){
            return ("\$a + \$b = ".($a+$b)."<br />\n");
        };
        
        $obj->myFunc(123,321);
        
    ```