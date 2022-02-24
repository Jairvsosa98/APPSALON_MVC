<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;



class LoginController {
    public static function login(Router $router) {
        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                // Comprobar que exista el usuario
                $usuario = Usuario::where('email', $auth->email);

                if($usuario) {
                    // Verificar el password
                    if($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        // Autenticar al usuario
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombres . " " . $usuario->apellidos;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionamiento
                        if($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin ?? null;

                            header('Location: /admin');
                        }else {
                            header('Location: /cita');
                        }

                        debuguear($_SESSION);
                    }

                }else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }
        $alertas = Usuario::getAlertas();

       $router->render('auth/login', [
           'alertas' => $alertas
       ]); 
    }
     
    public static function logout(Router $router) {

        
        $_SESSION = [];

        header('Location: /');
    }

    public static function olvide(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);
                if($usuario && $usuario->confirmado === "1") {
                    $usuario->crearToken();
                    $usuario->guardar();

                    // Enviar email
                    $email = new Email($usuario->nombres,$usuario->email,$usuario->token);
                    $email->enviarInstrucciones();

                    // Alerta de éxito
                    Usuario::setAlerta('exito', 'Revisa tu email');
                }else {
                    Usuario::setAlerta('error', 'El Usuario no existe o no está confirmado');
                    
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router) {
        $alertas = [];
        $token = s($_GET['token']);
        $error = false;

        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            $password->validarPassword();

            if(empty($alertas)) {
                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;
                $resultado = $usuario->guardar();
                if($resultado) {
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function crear(Router $router) {
        $usuario = new Usuario($_POST); 
        
        // Alertar vacías
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            // Revisar que alerta este vacio
            if(empty($alertas)) {
                // Verificar que el usuario no esté registrado
               $resultado = $usuario->existeUsuario();

               if($resultado->num_rows) {
                   $alertas = Usuario::getAlertas();
               }else {
                   // Hashear el Password
                   $usuario->hashPassword();

                   // Generar un Token único
                   $usuario->crearToken();

                   // Enviar el Email
                   $email = new Email($usuario->nombres, $usuario->email, $usuario->token);
                   $email->enviarConfirmacion();
                
                   // Crear el usuario
                   $resultado = $usuario->guardar();
                   if($resultado){
                       header('Location: /mensaje');
                   }
                   
                //    debuguear($usuario);
               }
            }
        }

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router) {
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router) {
        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);
        
        if(empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta('error',' Token no válido');
        }else {
            // Modificar a usuario confirmado
            $usuario->confirmado = 1;
            $usuario->token = null;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Tu cuenta ha sido comprobada correctamente');
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}