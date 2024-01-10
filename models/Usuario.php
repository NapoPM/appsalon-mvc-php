<?php

namespace Model;

class Usuario extends ActiveRecord{
    // Base de datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($arg = []) {
        $this->id = $arg['id'] ?? null;
        $this->nombre = $arg['nombre'] ?? '';
        $this->apellido = $arg['apellido'] ?? '';
        $this->email = $arg['email'] ?? '';
        $this->password = $arg['password'] ?? '';
        $this->telefono = $arg['telefono'] ?? '';
        $this->admin = $arg['admin'] ?? 0;
        $this->confirmado = $arg['confirmado'] ?? 0;
        $this->token = $arg['token'] ?? '';
    }


    // MENSAJES DE VALIDACIÓN
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'El Nombre OBLIGATORIO.';
        }
        if(!$this->apellido){
            self::$alertas['error'][] = 'El Apellido OBLIGATORIO.';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El Email OBLIGATORIO.';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'La Contraseña OBLIGATORIO.';
        }
        if(!$this->telefono){
            self::$alertas['error'][] = 'El Teléfono OBLIGATORIO.';
        }
        if(strlen($this->password < 6)){
            self::$alertas['error'][] = 'La contraseña debe contener más de 6 caracteres..';
        }

        return self::$alertas;
    }

    public function validarLogin(){
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es obligatorio';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'La Contraseña es obligatoria';
        }

        return self::$alertas;
    }

    // Revisa si el usuario ya existe
    public function existeUsuario(){
        $query = " SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";

        $resultado = self::$db->query($query);

        if($resultado->num_rows){
            self::$alertas['error'][] = 'El Usuario ya está registrado.';
        }

        return $resultado;
    }

    public function validarEmail(){
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es obligatorio';
        }
        return self::$alertas; 
    }

    public function validarPassword(){
        if (!$this->password ) {
            self::$alertas['error'][] = 'El Password es obligatorio';
        }
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres.';
        }
        return self::$alertas;
    }

    public function hashpassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken(){
        $this->token = uniqid();
    }

    public function comprobarPassword($password){
        $resultado = password_verify($password, $this->password);
        if (!$resultado || !$this->confirmado) {
            self::$alertas['error'][] = 'Contraseña incorrecta o usuario no confirmada.';
        }else{
            return true;
        }
    }
}