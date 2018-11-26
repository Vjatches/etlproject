<?php

/**
 * Class Timer
 * class which helps to measure time between processes
 */
class Timer
{
    /**
     * Starting time
     * @var
     */
    private $start_time;

    /**
     * Function which is used to start timer
     */
    public function start(){
        $this->start_time=microtime(1);
    }

    /**
     * Function which is used to stop timer
     * @return string - time
     */
    public function stop(){
        $end_time=microtime(1);
        return getTime($this->start_time,$end_time);
    }
}