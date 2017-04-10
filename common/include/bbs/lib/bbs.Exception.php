<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/10/31
 * Time: 14:08
 */
class bbsException extends Exception {
    public function errorMessage()
    {
        return $this->getMessage();
    }

    public function errorTrace(){
        return $this->getTrace();
    }

}