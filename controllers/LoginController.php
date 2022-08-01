<?php

namespace Controllers;

use MVC\Router;
use Classes\Email;
use Model\Usuario;

class LoginController{


    public static function login(Router $router){
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === "POST"){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarLogin();

            if(empty($alertas)){
                // Verificar si el usuario exista

                $usuario = Usuario::where('email', $usuario->email);
                
                if(!$usuario || !$usuario->confirmado){
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                } else{
                    // El usuario existe
                    if( password_verify($_POST['password'], $usuario->password)){
                        // Inciiar la sesión
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] =  true;

                        // Redireccionar

                        header('location: /dashboard');

                    } else {
                        Usuario::setAlerta('error', 'Password Incorrecto');
                    }

                }
                
            }
        }
        // Render a la vista 
        $alertas = Usuario::getAlertas();
        $router->render('auth/login',[
            'titulo' => 'Iniciar Sesion',
            'alertas' => $alertas
        ]);
    }


    public static function logout(){
        session_start();
        $_SESSION = [];
        header('location: /');

    }

    
    public static function crear(Router $router){
        
        $usuario = new Usuario();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === "POST"){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if(empty($alertas)){
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario){
                Usuario::setAlerta('error', 'El usuario ya esta registrado');
                $alertas = Usuario::getAlertas();
            } else {
                // Hashear el password 
                $usuario->hashPassword();

                // Eliminar password2
                unset($usuario->password2);

                // Generar el token 

                $usuario->crearToken();

                
                // Crear un nuevo usuario 
                $resultado = $usuario->guardar();
                
                // Mandar Email 
                $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
            
                
                $email->enviarConfirmacion();

                if($resultado){
                    header('location: /mensaje');
                }
            }
            }
        }

        $router->render('auth/crear',[
            'titulo' => 'Crea tu Cuenta en Uptask',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router){
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === "POST"){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)){

                // Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if($usuario && $usuario->confirmado){
                    // Generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->passowrd2);
                    // Actualizar el usuario
                    $usuario->guardar();
                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    $email->enviarInstruccion();
                    // Imprimir la alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                    
                }
                
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/olvide',[
            'titulo' => 'Recuperar Password',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router){
        
        $alertas = [];
        $token = s($_GET['token']);
        $mostrar = true;

        if(!$token) header('location: /');

        // Identificar al usuario con este token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token No Valido');
            $mostrar = false;
        }
        if($_SERVER['REQUEST_METHOD'] === "POST"){
            //Añadir el nuevo password
            $usuario->sincronizar($_POST);

            // Validar el passowrd
            $alertas = $usuario->validarPassword();

            if(empty($alertas)){
                //Hashear el nuevo password
                $usuario->hashPassword();
                // Eliminar el token
                $usuario->token = null;
                // Guardar el usuario en la bd
                $resultado = $usuario->guardar();
                // Redireccionar

                if($resultado){
                    header('location: /');
                }

            }

        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/reestablecer',[
            'titulo' => 'Reestablecer',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router){
        
        $router->render('auth/mensaje',[
            'titulo' => 'Cuenta Creada Exitosamente'
        ]);
    
    }

    public static function confirmar(Router $router){
        $token = s($_GET['token']);

        if(!$token) header ('location: /');

        // Encontrar al usuario con este token 

        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            // No se encontro la cuenta
            Usuario::setAlerta('error', 'Token No Valido');
        } else {
            // Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = ' ';
            unset($usuario->password2);
            
            // Guardar en la bd
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');

        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar',[
            'alertas' => $alertas,
            'titulo' => 'Confirma tu cuenta UpTask'
        ]);
    
    }
}
