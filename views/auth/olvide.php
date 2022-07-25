<div class="contenedor olvide">
<?php include_once __DIR__ . '/../templates/sitios.php'; ?>


    <div class="contenedor-sm">
        <p class="descripcion-pagina">Recuperar Contraseña</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form action="/olvide" method="POST" class="formulario" novalidate>
        <!--Email-->
            <div class="campo">
                <label for="email">Email</label>
                <input 
                type="email"
                id="email"
                placeholder="Tu Email"
                name="email"
                />
            </div>

            <input type="submit" class="boton" value="Enviar Instrucciones">
        </form>
        <div class="acciones">
            <a href="/crear">¿Aún no tienes una cuenta? Crear Una Cuenta</a>
            <a href="/">¿Ya tienes una cuenta? Iniciar Sesión</a>
        </div>
    </div> <!--.contenedor-sm -->
</div>