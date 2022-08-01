<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Proyecto;

class DashboardController
{
    public static function index(Router $router)
    {

        session_start();
        isAuth();

        $id = $_SESSION['id'];
        // debuguear($id);

        $proyectos = Proyecto::belongsTo('propietarioId', $id);
        // $proyecto = [];
        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }


    public static function crear_proyecto(Router $router)
    {
        session_start();
        isAuth();

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $proyecto = new Proyecto($_POST);

            // Validacion
            $alertas = $proyecto->validarProyecto();

            if (empty($alertas)) {
                // Generar una url UNICA
                $hash = md5(uniqid());
                $proyecto->url = $hash;
                // Elimina los espacios en blanco
                $proyecto->proyecto = trim($proyecto->proyecto);
                // Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];
                // debuguear($proyecto);
                // Guardar el proyecto
                $proyecto->guardar();

                // Redireccionar 
                header('location: /proyecto?id=' . $proyecto->url);
            }
        }

        $router->render('dashboard/crear-proyecto', [
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }

    public static function perfil(Router $router)
    {
        session_start();
        isAuth();

        $alertas = [];
        $usuario = Usuario::find($_SESSION['id']);
        // debuguear($usuario);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $usuario->sincronizar($_POST);
            $usuario->nombre = trim($usuario->nombre);
            $alertas = $usuario->validarActualizarCuenta();

            if (empty($alertas)) {
                // Verificar si el email existe
                $existeUsuario = Usuario::where('email', $usuario->email);

                if ($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    // Mostrar un mensaje de error
                    Usuario::setAlerta('error', 'El Email ya pertenece a una cuenta registrada');
                    $alertas = Usuario::getAlertas();


                } else {
                    // Guardar Usuario
                    $resultado = $usuario->guardar();

                    Usuario::setAlerta('exito', 'Se Ha Guardado Correctamente');
                    $alertas = Usuario::getAlertas();
                    // Asignar el nombre nuevo a la barra
                    $_SESSION['nombre'] = $usuario->nombre;
                }
            }
        }




        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' => $alertas

        ]);
    }

    public static function proyecto(Router $router)
    {
        session_start();
        isAuth();

        $token = $_GET['id'];

        if (!$token) header('location: /dashboard');
        // Revisar que la persona que visita el proyecto es quien lo creo
        $proyecto = Proyecto::where('url', $token);

        // debuguear($proyecto);
        if ($proyecto->propietarioId !== $_SESSION['id']) {
            header('location: /dashboard');
        }
        $nombre = $proyecto->proyecto;
        $alertas = [];

        $router->render('dashboard/proyecto', [
            'titulo' => $nombre,
            'alertas' => $alertas
        ]);
    }

    public static function cambiar_password(Router $router){
        session_start();
        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === "POST"){
            $usuario = Usuario::find($_SESSION['id']);
            
            // Sincronizar con los datos del usuario
            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password();
            
            if(empty($alertas)){
                $resultado = $usuario->comprobar_password();

                if($resultado){
                    // Asignar el nuevo password
                    $usuario->password = $usuario->password_nuevo;
                    // Eliminar propiedades NO necesarias
                    unset ($usuario->password_actual);
                    unset($usuario->password_nuevo);

                    // Hashear el  nuevo password
                    $usuario->hashPassword();
                    // Actualizar 
                    $resp = $usuario->guardar();

                    if($resp){
                        Usuario::setAlerta('exito', 'Password Guardado Correctamente');
                        $alertas = $usuario->getAlertas(); 
                    }
                } else {
                    Usuario::setAlerta('error', 'Password Incorrecto');
                    $alertas = $usuario->getAlertas(); 
                }
            }

        }

        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambio De Password',
            'alertas' => $alertas
        ]); 
    }
}
