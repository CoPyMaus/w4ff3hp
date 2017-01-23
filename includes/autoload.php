<?php
function __autoload_my_classes($class_name)
{
    require 'core/'.$class_name . '.class.php';
}
spl_autoload_register('__autoload_my_classes');
?>