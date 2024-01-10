<?php

namespace Model;

class Cita extends ActiveRecord {
    // Base de Datos
    protected static $tabla = 'citas';
    protected static $columnasDB = ['id', 'fecha', 'hora', 'UsuarioId'];

    public $id;
    public $fecha;
    public $hora;
    public $UsuarioId;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->fecha = $args['fecha'] ?? '';
        $this->hora = $args['hora'] ?? '';
        $this->UsuarioId = $args['UsuarioId'] ?? '';
    }

}