<div class="contenedor reestablecer">
<?php include_once __DIR__ . '/../templates/sitios.php'; ?>


    <div class="contenedor-sm">
        <p class="descripcion-pagina">Coloca Tu Nuevo Password</p>
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
        <?php if($mostrar) { ?>
        <form method="POST" class="formulario">
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

            <input type="submit" class="boton" value="Guardar Password">
        </form>

        <?php } ?> 
        <div class="acciones">
            <a href="/crear">¿Aún no tienes una cuenta? Crear Una Cuenta</a>
            <a href="/olvide">¿Olvidaste tu contraseña?</a>
        </div>
    </div> <!--.contenedor-sm -->
</div>