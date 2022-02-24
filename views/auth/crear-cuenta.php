<h1 class="nombre-pagina">Crear cuenta</h1>
<p class="descripcion-pagina">Llena el siguiente formulario para crear una cuenta</p>

<?php include_once __DIR__ . "/../templates/alertas.php"; ?>

<form class="formulario" action="/crear-cuenta" method="POST">
    <div class="campo">
        <label for="nombres">Nombres</label>
        <input
        type="text"
        id="nombres"
        name="nombres"
        placeholder="Tu Nombre"
        value="<?php echo s($usuario->nombres); ?>"
        />
    </div>

    <div class="campo">
        <label for="apellidos">Apellidos</label>
        <input
        type="text"
        id="apellidos"
        name="apellidos"
        placeholder="Tu Apellidos"
        value="<?php echo s($usuario->apellidos); ?>"
        />
    </div>

    <div class="campo">
        <label for="telefono">Telefono</label>
        <input
        type="tel"
        id="telefono"
        name="telefono"
        placeholder="Tu Telefono"
        value="<?php echo s($usuario->telefono); ?>"
        />
    </div>

    <div class="campo">
        <label for="email">Email</label>
        <input
        type="email "
        id="email"
        name="email"
        placeholder="Tu Email"
        value="<?php echo s($usuario->email); ?>"
        />
    </div>

    <div class="campo">
        <label for="password">Password</label>
        <input
        type="password"
        id="password"
        name="password"
        placeholder="Tu Password"
        value="<?php echo s($usuario->password); ?>"
        />
    </div>

    <input type="submit" class="boton" value="Crear Cuenta" />
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
    <a href="/olvide">¿Olvidaste tu Password?</a>
</div>