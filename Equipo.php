<?php

require_once 'Conexion.php';
$dbh = new Conexion;

class Equipo
{
    public function __construct($nombre, $torneo)
    {
        $this->nombre = $nombre;
        $this->torneo = $torneo;
    }

    public function getDatosEquipo()
    {
        global $dbh;
        $sth = $dbh->prepare('select * from equipos where nombre_equipo=:equipo and torneo=:torneo');
        $sth->execute([':equipo' => $this->nombre, ':torneo' => $this->torneo]);
        $datos_equipo = $sth->fetch(PDO::FETCH_ASSOC);
        return $datos_equipo;
    }

    public function getDatosDelegado($dni)
    {
        global $dbh;
        $sth = $dbh->prepare('select documento, apellidos, nombres from personas where documento = :dni');
        $sth->execute([':dni' => $dni]);
        $datos_delegado = $sth->fetch(PDO::FETCH_ASSOC);
        return $datos_delegado;
    }
}
