<h1 class="nombre-pagina">Crear Cuenta</h1>
<p class="descripcion-pagina">Llena el siguiente formulario para crear una cuenta.</p>

<?php
    include_once __DIR__ . "/../templates/alertas.php";
?>


<form method="POST" action="/crear-cuenta" class="formulario">
    <div class="campo">
        <label for="Nombre">Nombre</label>
        <input 
        type="text" 
        id="nombre"
        name="nombre"
        placeholder="Tu Nombre"
        value="<?php echo s($usuario->nombre); ?>"
        />
    </div>
    
    <div class="campo">
        <label for="Apellido">Apellido</label>
        <input 
        type="text" 
        id="apellido"
        name="apellido"
        placeholder="Tu Apellido"
        value="<?php echo s($usuario->apellido); ?>"
        />
    </div>

    <div class="campo">
        <label for="Telefono">Telefono</label>
        <input 
        type="tel" 
        id="telefono"
        name="telefono"
        placeholder="Tu Telefono"
        value="<?php echo s($usuario->telefono); ?>"
        />
    </div>

    <div class="campo">
        <label for="email">E-mail</label>
        <input 
        type="email" 
        id="email"
        name="email"
        placeholder="Tu Email"
        value="<?php echo s($usuario->email); ?>"
        />
    </div>

    <div class="campo">
        <label for="password">Contraseña</label>
        <input 
        type="password" 
        id="password"
        name="password"
        placeholder="Tu Contraseña"
        />
    </div>

    <input type="submit" class="boton" value="Crear Cuenta">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesión.</a>
    <a href="/olvide">¿No recuerdas la contraseña? Cambiala aquí.</a>
</div>