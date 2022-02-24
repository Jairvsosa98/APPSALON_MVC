<?php

namespace Model;

class Usuario extends ActiveRecord {
    // Base de datos
    protected static $tabla ='usuarios';
    protected static $columnasDB = ['id','nombres','apellidos', 'email', 'password', 'telefono', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombres;
    public $apellidos;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = []){
        $this->id = $args['id'] ?? null;
        $this->nombres = $args['nombres'] ?? '';
        $this->apellidos = $args['apellidos'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? "0";
        $this->confirmado = $args['confirmado'] ?? "0";
        $this->token = $args['token'] ?? '';
    }

    // Mensajes de validación para la creación de una cuenta
    public function validarNuevaCuenta() {
        if(!$this->nombres) {
            self::$alertas['error'][] = 'Los Nombres son Obligatorios';
        }
        if(!$this->apellidos) {
            self::$alertas['error'][] = 'Los Apellidos son Obligatorios';
        }
        if(!$this->email) {
            self::$alertas['error'][] = 'El E-mail es Obligatorio';
        }
        if(!$this->password) {
            self::$alertas['error'][] = 'El Password es Obligatorio';
        }
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres';
        }
        if(!$this->telefono) {
            self::$alertas['error'][] = 'El Teléfono es Obligatorio';
        }
        if(strlen($this->telefono) < 10) {
            self::$alertas['error'][] = 'El Teléfono debe contener al menos 10 caracteres';
        }

        return self::$alertas;
    }

    public function validarEmail() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El E-mail es Obligatorio';
        }
        return self::$alertas;
    }

    public function validarPassword(){
        if(!$this->password) {
            self::$alertas['error'][] = 'El Password es Obligatorio';
        }
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    // Revisa si el suuario ya existe
    public function existeUsuario() {
        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '". $this->email . "' LIMIT 1";

        $resultado = self::$db->query($query);
        
        if($resultado->num_rows) {
            self::$alertas['error'][] = 'El usuario ya está registrado';
        }
        return $resultado;
    }

    public function validarLogin() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El E-mail es Obligatorio';
        }
        if(!$this->password) {
            self::$alertas['error'][] = 'El Password es Obligatorio';
        }
        return self::$alertas;
    }

    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken() {
        $this->token = uniqid();
    }

    public function comprobarPasswordAndVerificado($password) {
        $resultado = password_verify($password,$this->password);
        if(!$resultado || !$this->confirmado) {
            self::$alertas['error'][] = 'Password incorrecto o tu cuenta no ha sido confirmada';
        }else {
            return true;
        }
    }
   

}