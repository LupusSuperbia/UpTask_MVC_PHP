<?php 

namespace Model;

class Usuario extends ActiveRecord{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    // public $id;
    // public $nombre;
    // public $email;
    // public $password;
    // public $token;
    // public $confirmado;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    // ----------------------- Validar Login ----------------------- // 

    public function validarLogin() : array{
        if(!$this->email){
            self::$alertas['error'][]= 'El email del usuario es obligatorio';
        }
        if(!filter_var($this->email,FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = "Email No valido";
        }
        if(!$this->password){
            self::$alertas['error'][]= 'El password no puede ir vacio';
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][]= 'El password debe contener al menos 6 caracteres';
        }

        return self::$alertas;
    }


    // ----------------------- Validacion para cuenta nuevas ----------------------- // 

    public function validarNuevaCuenta() : array {
        if(!$this->nombre){
            self::$alertas['error'][]= 'El nombre del usuario es obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][]= 'El email del usuario es obligatorio';
        }
        if(!$this->password){
            self::$alertas['error'][]= 'El password no puede ir vacio';
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][]= 'El password debe contener al menos 6 caracteres';
        }
        if($this->password !== $this->password2) {
            self::$alertas['error'][]= 'Los Password Son diferentes';
        }

        return self::$alertas;
    }


    public function nuevo_password() : array{
        if(!$this->password_actual){
            self::$alertas['error'][] = 'El Password Actual no puede ir vacio';
        }
        if(!$this->password_nuevo){
            self::$alertas['error'][] = 'El Password Nuevo no puede ir vacio';
        }
        if(strlen($this->password_nuevo) < 6){
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    public function validarPassword(){
        if(!$this->password){
            self::$alertas['error'][]= 'El password no puede ir vacio';
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][]= 'El password debe contener al menos 6 caracteres';
        }
        if($this->password !== $this->password2) {
            self::$alertas['error'][]= 'Los Password Son diferentes';
        }

        return self::$alertas;
    }
    // Comprobar el password
    public function comprobar_password() : bool {
        return password_verify($this->password_actual, $this->password);
    }
    // Hashe el password
    public function hashPassword() : void {
        $this-> password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    // ----------------------- Generar Un Token ----------------------- // 

    public function crearToken() : void {
        $this->token = uniqid();
    }

    // ----------------------- validadEmail ----------------------- // 

    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] = "El email es obligatorio";
        }
        if(!filter_var($this->email,FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = "Email No valido";
        }

        return self::$alertas;
    }

    public function validarActualizarCuenta(){
        if(!$this->nombre){
            self::$alertas['error'][]= 'El nombre del usuario es obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = "El email es obligatorio";
        }
        if(!filter_var($this->email,FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = "Email No valido";
        }

        return self::$alertas;
    }
}