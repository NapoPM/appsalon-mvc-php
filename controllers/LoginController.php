<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController{
    public static function login(Router $router){
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();
        
        if (empty($alertas)) {
            // Comprobar que el usuario exista
            $usuario = Usuario::where('email', $auth->email);

            if ($usuario) {
                // Verificar password
                if ($usuario->comprobarPassword($auth->password)){
                    // Autenticar el usuario
                    if(!isset($_SESSION)) {
                        session_start();
                    };

                    $_SESSION['id'] = $usuario->id;
                    $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                    $_SESSION['email'] = $usuario->email;
                    $_SESSION['login'] = true;

                    // Redireccionamiento
                    if ($usuario->admin == "1") {
                       $_SESSION['admin'] = $usuario->admin ?? null;
                       header('Location: /admin');
                    }else{
                        header('Location: /cita');
                    }
                };
            }else{
                Usuario::setAlerta('error', 'Usuario no encontrado');
            }
        }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/login',[
            'alertas' => $alertas,
        ]);
    }
    public static function logout(Router $router){
        session_start();

        $_SESSION = [];
        header('Location: /');
    }

    public static function olvide(Router $router){
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();
            
            if (empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);
                
                if ($usuario && $usuario->confirmado === "1") {

                    //Generar Token
                    $usuario->crearToken();
                    $usuario->guardar();

                    // Enviar email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarInstrucciones();

                    // Alerta éxito.
                    Usuario::setAlerta('exito', 'Revista tu email');
                    $alertas = Usuario::getAlertas();
                }else{
                    Usuario::setAlerta('error', 'El usuario no está confirmado o no existe.');
                    $alertas = Usuario::getAlertas();
                };
            }
        }

        $router->render('auth/olvide-password',[
            'alertas' => $alertas
        ]);
    }
    public static function recuperar(Router $router){
        $alertas = [];
        $error = false;
        $token = s($_GET['token']);

        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        
        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no valido');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            // Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();
            
            if (empty($alertas)) {
                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashpassword();
                $usuario->token = null;
                $resultado = $usuario->guardar();
                if ($resultado) {
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

    public static function crear(Router $router){
        $usuario = new Usuario;

        // Alertas vacias
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();


            if (empty($alertas)) {
                // Verificar que el usuario no esté registrado.
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows){
                    $alertas = Usuario::getAlertas();
                }else{
                    // Hashear el password
                    $usuario->hashPassword();
                    
                    // Generar un TOKEN
                    $usuario->crearToken();
                    
                    // Enviar Email
                    $email = New Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarInstrucciones();

                    
                    // Crear el USUARIO
                    $resultado = $usuario->guardar();

                    // debuguear($usuario);

                    if ($resultado) {
                        header('Location: /mensaje');
                    }

                }
            }
        }

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje');
    }
    public static function confirmar(Router $router){
        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);
        if (empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'TOKEN NO VALIDO.');
        }else{
            // Modificar a usuario confirmado
            $usuario->confirmado = '1';
            $usuario->token = "";
            $usuario->guardar();
            Usuario::setAlerta('exito', 'TOKEN VALIDO');
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}