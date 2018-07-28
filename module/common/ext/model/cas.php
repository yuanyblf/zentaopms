<?php
public function isOpenMethod($module, $method)
{   
    if($module == 'cas') return true;
    return parent::isOpenMethod($module, $method);
}