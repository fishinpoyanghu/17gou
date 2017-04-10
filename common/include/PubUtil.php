<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/10
 * Time: 18:47
 */

class PubUtil{

    public static function rand_ip(){
        $file = file_get_contents(dirname(__FILE__).'/ip.txt');
        $ip = explode('|',$file);
        shuffle($ip);
        return array_pop($ip);
    }



}