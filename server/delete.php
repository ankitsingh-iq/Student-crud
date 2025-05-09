<?php

require_once __DIR__ . '/config/config.php';


$id=$_GET['id'];

$result=$conn->query("DELETE from students where id =$id");

if($result)
{
    echo("record delete");
}
else{
    echo ("Record DELETE failed");
}