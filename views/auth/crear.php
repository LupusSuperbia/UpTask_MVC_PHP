<div class="contenedor crear">
    <!-- HEADER  -->
<?php include_once __DIR__ . '/../templates/sitios.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Crear Una Cuenta</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form action="/crear" method="POST" class="formulario">
        <!-- Nombre -->
        <div class="campo">
                <label for="nombre">Nombre</label>
                <input 
                type="nombre"
                id="nombre"
                name="nombre"
                value="<?php  echo $usuario->nombre; ?>"
                placeholder="Tu Nombre"
                />
            </div>
        
        <!--Email-->
            <div class="campo">
                <label for="email">Email</label>
                <input 
                type="email"
                id="email"
                placeholder="Tu Email"
                name="email"
                value="<?php echo $usuario->email ?> "

                />
            </div>
        <!--Password -->
            <div class="campo">
                <label for="password">Password</label>
                <input 
                type="password"
                id="password"
                placeholder="Tu Password"
                name="password"
                />
            </div>
        <!-- Repetir Password -->
        <div class="campo">
                <label for="password2">Repetir Password</label>
                <input 
                type="password"
                id="password2"
                placeholder="Repite Tu Password"
                name="password2"
                />
            </div>
            <input type="submit" class="boton" value="Crear Cuenta">
        </form>
        <div class="acciones">
            <a href="/">¿Ya tienes una cuenta? Iniciar Sesión</a>
            <a href="/olvide">¿Olvidaste tu contraseña?</a>
        </div>
    </div> <!--.contenedor-sm -->
</div>