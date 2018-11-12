<?php
/**
 * Created by PhpStorm.
 * User: babanin
 * Date: 11/12/2018
 * Time: 13:14
 */

class Timer
{
    private $start_time;
    public function start(){
        $this->start_time=microtime(1);
    }
    public function stop(){
        $end_time=microtime(1);
        return getTime($this->start_time,$end_time);
    }
}