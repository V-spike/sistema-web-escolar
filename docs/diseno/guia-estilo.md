# Guía de estilo

Esta guía explica los colores, la fuente y las clases de `public/css/estilos.css`, que usan todas las pantallas del Sistema Web Escolar.

**Reglas para el equipo**

- No se cambian los nombres de las clases sin avisar al equipo.
- Los colores se usan con su variable, por ejemplo `var(--color-primario)`, y no con el valor escrito a mano. Así, si cambia un color, se cambia en un solo lugar.
- Las clases siguen la convención BEM: `bloque__elemento--variante`. En el HTML se escribe la clase base junto con su variante, por ejemplo `class="btn btn--primario"`.

## Colores

| Nombre | Variable CSS | Valor | Uso |
| ---    | ---          | -  -- | --- |
|primario| `--color-primario` | #1F4E79 | Botones principales, encabezado, enlaces y texto de la opción activa del menú |
| primario claro | `--color-primario-claro` | #DCE6F2 | Fondo de la opción activa del menú |
| fondo | `--color-fondo` | #F5F7FA | Fondo general de las pantallas |
| texto | `--color-texto` | #222222 | Texto principal |
| borde | `--color-borde` | #C9D3DF | Bordes de los campos y del menú |
| éxito | `--color-exito` | #2E7D32 | Texto de los mensajes de éxito |
| error | `--color-error` | #C62828 | Texto de los mensajes de error y borde de un campo con error |
| aviso | (sin variable) | #B7791F | Reservado para advertencias. Todavía no tiene variable ni clase en `estilos.css` |

Otros valores fijos que usa el CSS:

| Valor | Uso |
| --- | --- |
| #FDECEA | Fondo del mensaje de error |
| #E8F5E9 | Fondo del mensaje de éxito |
| #9AA5B1 | Texto de una opción de menú pendiente |
| #FFFFFF | Tarjetas, menú y botón secundario |

## Fuente y medidas

- Fuente: Segoe UI, con respaldo Arial y sans-serif (`--fuente`).
- Esquinas redondeadas: 6 px (`--radio`). Las tarjetas usan 10 px.
- Tamaño de texto base de botones y campos: 1 rem (16 px).

## Clases

### Botones

| Clase | Para qué sirve | Ejemplo |
| --- | --- | --- |
| `.btn` | Forma base de todo botón (relleno, esquinas, cursor). Siempre va junto con una variante | `<button class="btn btn--primario">Guardar</button>` |
| `.btn--primario` | Acción principal de la pantalla. Fondo azul, texto blanco y ocupa todo el ancho disponible | `<button class="btn btn--primario">Entrar</button>` |
| `.btn--secundario` | Acción secundaria. Fondo blanco con borde azul | `<button class="btn btn--secundario">Cancelar</button>` |

### Formularios

| Clase | Para qué sirve | Ejemplo |
| --- | --- | --- |
| `.campo__etiqueta` | Texto que va encima del campo, en seminegrita | `<label class="campo__etiqueta" for="usuario">Usuario</label>` |
| `.campo` | Cuadro donde se escribe, ocupa todo el ancho disponible | `<input class="campo" id="usuario" type="text">` |
| `.campo--error` | Campo con un dato inválido (borde rojo). Se combina con `.campo` | `<input class="campo campo--error" id="usuario" type="text">` |

### Mensajes

| Clase | Para qué sirve | Ejemplo |
| --- | --- | --- |
| `.mensaje` | Forma base de todo mensaje. Siempre va junto con una variante | `<div class="mensaje mensaje--exito">Guardado correctamente.</div>` |
| `.mensaje--exito` | Operación correcta (fondo verde claro) | `<div class="mensaje mensaje--exito">Calificaciones guardadas.</div>` |
| `.mensaje--error` | Algo salió mal o un dato es inválido (fondo rojo claro) | `<div class="mensaje mensaje--error">Usuario o contraseña incorrectos.</div>` |

### Pantallas sin sesión (login)

| Clase | Para qué sirve | Ejemplo |
| --- | --- | --- |
| `.contenedor-centrado` | Ocupa toda la altura de la pantalla y centra su contenido | `<div class="contenedor-centrado">...</div>` |
| `.tarjeta` | Caja blanca con esquinas redondeadas y sombra suave | `<div class="tarjeta">...</div>` |
| `.tarjeta--login` | Variante de la tarjeta con ancho máximo de 380 px | `<div class="tarjeta tarjeta--login">...</div>` |
| `.enlace` | Enlace azul, centrado y en su propia línea | `<a class="enlace" href="#">Volver al inicio</a>` |

Estructura completa de un login:

```html
<div class="contenedor-centrado">
    <div class="tarjeta tarjeta--login">
        <h1>Iniciar sesión</h1>

        <div class="mensaje mensaje--error">Usuario o contraseña incorrectos.</div>

        <label class="campo__etiqueta" for="usuario">Usuario</label>
        <input class="campo" id="usuario" type="text">

        <label class="campo__etiqueta" for="clave">Contraseña</label>
        <input class="campo" id="clave" type="password">

        <button class="btn btn--primario">Entrar</button>
        <a class="enlace" href="#">Volver al inicio</a>
    </div>
</div>
```

### Pantallas con sesión (paneles)

| Clase | Para qué sirve | Ejemplo |
| --- | --- | --- |
| `.encabezado` | Barra azul superior, con el título a la izquierda y el usuario a la derecha | `<header class="encabezado">...</header>` |
| `.encabezado__titulo` | Nombre del sistema dentro del encabezado, en negrita | `<div class="encabezado__titulo">Sistema Web Escolar</div>` |
| `.encabezado__usuario` | Grupo con el nombre del usuario y el botón de cerrar sesión | `<div class="encabezado__usuario">...</div>` |
| `.distribucion` | Divide la pantalla en dos columnas: menú (220 px) y contenido | `<div class="distribucion">...</div>` |
| `.menu` | Menú lateral blanco con las opciones del perfil | `<nav class="menu">...</nav>` |
| `.menu__opcion` | Una opción del menú | `<a class="menu__opcion" href="#">Mi horario</a>` |
| `.menu__opcion--activa` | Opción de la pantalla actual (fondo azul claro y texto en negrita) | `<a class="menu__opcion menu__opcion--activa" href="#">Inicio</a>` |
| `.menu__opcion--pendiente` | Opción de una pantalla que todavía no está disponible (gris, cursor de "no permitido") | `<a class="menu__opcion menu__opcion--pendiente">Mi boleta</a>` |
| `.contenido` | Zona principal de la pantalla, a la derecha del menú | `<main class="contenido">...</main>` |

Estructura completa de un panel:

```html
<header class="encabezado">
    <div class="encabezado__titulo">Sistema Web Escolar</div>
    <div class="encabezado__usuario">
        <span>Nombre del usuario</span>
        <button class="btn btn--secundario">Cerrar sesión</button>
    </div>
</header>

<div class="distribucion">
    <nav class="menu">
        <a class="menu__opcion menu__opcion--activa" href="#">Inicio</a>
        <a class="menu__opcion" href="#">Mi horario</a>
        <a class="menu__opcion menu__opcion--pendiente">Mi boleta</a>
    </nav>

    <main class="contenido">
        <h1>Inicio</h1>
    </main>
</div>
```

## Pantallas pequeñas (celular)

Cuando la pantalla mide 700 px de ancho o menos:

- `.distribucion` pasa a una sola columna: el menú queda arriba y el contenido debajo.
- `.menu` se convierte en una fila horizontal con desplazamiento lateral, y su borde pasa de la derecha a la parte inferior.

No hace falta agregar clases nuevas: el cambio es automático al usar las clases anteriores.