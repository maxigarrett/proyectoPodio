<?php
require_once 'config/config.php';

class Conexion extends PDO{
    function __construct()
    {
        parent::__construct(HOST_DB,USER,PASS);
    }
}
