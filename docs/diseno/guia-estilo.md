# Guía de estilo

Esta guía explica los colores, la fuente y las clases de `public/css/estilos.css`, que usan todas las pantallas del Sistema Web Escolar.

**Reglas para el equipo**

- No se cambian los nombres de las clases sin avisar al equipo.
- Los colores se usan con su variable, por ejemplo `var(--color-primario)`, y no con el valor escrito a mano. Así, si cambia un color, se cambia en un solo lugar.
- Las clases siguen la convención BEM: `bloque__elemento--variante`. En el HTML se escribe la clase base junto con su variante, por ejemplo `class="btn btn--primario"`.

## Colores

Diseño final (26/11/2026): minimalista, con los dos colores del logo de la escuela. Todo lo demás es blanco y gris. Las variables están al inicio de `public/css/estilos.css`.

| Nombre | Variable CSS | Valor | Uso |
| --- | --- | --- | --- |
| primario (azul del logo) | `--primario` | #1F395A | Botones principales, nombre del sistema, pestaña activa, números del inicio |
| primario suave | `--primario-suave` | #EDF1F6 | Fondo de la opción activa del menú y de la columna que se captura |
| acento (verde del logo) | `--acento` | #62B22D | Rayita de la opción activa, barras de avance, línea del login |
| acento oscuro | `--acento-oscuro` | #3E7A1C | Verde para texto (se lee mejor sobre blanco) |
| fondo | `--fondo` | #FAFAFA | Fondo general |
| superficie | `--superficie` | #FFFFFF | Tarjetas, tablas y formularios |
| texto | `--texto` | #18181B | Texto principal |
| texto suave / tenue | `--texto-suave` / `--texto-tenue` | #52525B / #A1A1AA | Textos de ayuda y datos secundarios |
| borde | `--borde` | #E4E4E7 | Líneas de tablas, tarjetas y campos |
| éxito | `--exito` | #3E7A1C | Mensajes de éxito y estado "Activo" |
| error | `--error` | #B91C1C | Errores, empalmes y calificaciones reprobatorias |
| aviso | `--aviso` | #A16207 | Avisos que no impiden guardar |

Las variables viejas (`--color-primario`, `--color-fondo`, etc.) siguen existiendo y apuntan a las nuevas, para no romper nada.

## Fuente y medidas

- Fuente: Inter (Google Fonts), con respaldo Segoe UI y system-ui (`--fuente`). Una sola fuente para títulos y texto.
- Esquinas redondeadas: 8 px (`--radio`) en tarjetas y tablas; 6 px (`--radio-chico`) en botones y campos.
- Texto base: 14 px. Sin sombras ni degradados, salvo la tarjeta del login.
- Logo: `public/img/logo-escuela.png` (fondo transparente) y `public/img/favicon-escuela.png`.
- Fondo del login: `public/img/patron-escolar.svg`, íconos escolares en línea fina que se repiten.

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



## CLASES CATALOGOS 
1. Catálogos: barra de título y acciones
# Clase .barra-catalogo
Esta lase organiza horisontalmente los elementos que se encuentran dentro de la barra del catalogo, por ejemplo, un titulo y un boton para gregar un nuevo registro. 

Selector .barra-catalogo h1
Selecciona unicamente las etiquetas <h1> que esten dentro de un elemento con la clase .barra-catalogo
    -margin: 0; elimina los margenes predeterminados del encabezado.
    - Evita que el titulo tenga espacios adicionales que puedan desalinearlo con el boton. 

# Clase .btn--enlace
Sirve para dar apariencia de boton a un enlace HTML(<a>).

2. Catálogos: tabla del listado
Esta seccion se encarga de dar formato a las tablas donde se muestran los registros de un catálogo.
# Clase .tabla-contenedor
Es un contenedro que envuelve la tabla para controlar su apariencia y comportamiento.

# Clase .tabla-catalogo
Aplica el diseño general a la tabla
    - width: 100%; hace que ocupe todo el ancho disponible.
    - border-collapse: collapse; combina los bordes de las celdas para evitar separaciones dobles.

# Clase .tabla-catalogo__fila--baja
Sirve para identificar visualmente una fila que representa un registro dado de baja.
- color: #9AA5B1; cambia el texto a un tono gris.
- Permite diferenciar los registros inactivos de los activos.

# Clase .tabla-catalogo__acciones
Organiza los botones de acción de cada fila de la tabla.

Por ejemplo, los botones de editar y eliminar.
- display: flex; coloca los botones en una distribución flexible.
- gap: .5rem; establece separación entre ellos.
- align-items: center; los alinea verticalmente.


3. Etiquetas de estado
Estas clases permiten mostrar visualmente el estado de un registro mediante pequeñas etiquetas de colores.

# Clase .etiqueta
Es la clase base. Define el formato que compartirán todas las etiquetas.

# Clase .etiqueta--activa
Representa un registro activo.
- Fondo verde claro.
- Texto verde, utilizando la variable --color-exito.

# Clase .etiqueta--baja
Representa un registro dado de baja.
- Fondo rojo claro.
- Texto rojo, utilizando la variable --color-error.

# Clase .etiqueta--terminado
Representa un registro que ha finalizado o terminado.
- Fondo gris claro.
- Texto gris oscuro.

4. Botones pequeños de la tabla
# Clase .btn--chico
Modifica el tamaño de un botón para que ocupe menos espacio.
- padding: reduce el espacio interno.
- font-size: disminuye ligeramente el tamaño del texto.
Es especialmente útil cuando hay varios botones dentro de una tabla.

# Clase .btn--peligro
Se utiliza para identificar visualmente acciones peligrosas o destructivas, como eliminar un registro.
- background: #fff; establece el fondo blanco.
- color: var(--color-error); utiliza el color de error para el texto.
- border: 1px solid var(--color-error); agrega un borde del mismo color.
Generalmente se utiliza para botones de eliminación.

5. Formulario de catálogo
Esta sección define la apariencia de los formularios utilizados para agregar o modificar registros.
# Clase .formulario
La propiedad max-width permite que el formulario no se extienda demasiado en pantallas grandes.

# Clase .formulario__acciones
Organiza los botones que aparecen al final del formulario.

Por ejemplo:
- Guardar.
- Cancelar.
Sus propiedades permiten colocarlos en una misma fila, mantener una separación y agregar espacio respecto a los campos anteriores.

# Clase .campo__ayuda
Se utiliza para mostrar información adicional o instrucciones debajo de un campo del formulario.

## Cuadrícula semanal

Vista parcial reutilizable que dibuja la semana escolar (días × franjas).
Archivo: `resources/views/componentes/cuadricula.blade.php`

La cuadrícula **no sabe qué va dentro de cada celda**: recorre las franjas y los días,
y en cada cruce incluye la vista que le indiques. Así sirve para la disponibilidad
(casillas), el editor del horario (materias) y la impresión.

### Qué recibe

| Variable  | Obligatoria | Qué es |
|-----------|-------------|--------|
| `franjas` | Sí | Colección de franjas **en orden**, incluidos los recesos. Cada una con `id`, `hora_inicio`, `hora_fin` y `es_receso`. |
| `celda`   | Sí | Nombre de la vista que dibuja **una** celda (por ejemplo, `docentes._celda-disponibilidad`). |
| `datos`   | No | Arreglo con lo que necesita la vista de la celda (por ejemplo, `['marcadas' => $marcadas]`). Si no se manda, es `[]`. |

Los días salen de `config('escolar.dias')`. Si no existe, se usan de lunes a viernes.

### Cómo se incluye

```blade
@include('componentes.cuadricula', [
    'franjas' => $franjas,
    'celda'   => 'docentes._celda-disponibilidad',
    'datos'   => ['marcadas' => $marcadas],
])
```

### Qué recibe la vista de la celda

Cada celda recibe automáticamente:

- `$dia`: número del día (1 = lunes … 5 = viernes)
- `$franja`: la franja de esa fila
- todo lo que venga en `datos` (en el ejemplo, `$marcadas`)

Ejemplo mínimo de una vista de celda:

```blade
{{ $dia }}-{{ $franja->id }}
```

### Recesos

Las franjas con `es_receso = true` se dibujan como una sola fila de lado a lado
que dice "Receso". No llevan celdas, así que ahí no se pueden marcar casillas ni asignar materias.

### Clases CSS (en `public/css/estilos.css`)

| Clase | Uso |
|-------|-----|
| `.cuadricula` | La tabla. Ancho mínimo de 640px: en el celular se desliza dentro de `.tabla-contenedor`. |
| `.cuadricula__hora` | Columna de la hora (96px). Se queda fija al deslizar. |
| `.cuadricula__celda` | Cada celda de día
## Clases agregadas en la versión final

| Clase | Para qué sirve |
| --- | --- |
| `.menu__grupo`, `.menu__seccion`, `.icono` | Secciones del menú lateral con título y su ícono (`resources/views/componentes/icono.blade.php`) |
| `.marca`, `.marca__logo`, `.marca__escuela` | Logo y nombre de la escuela (`componentes/marca.blade.php`) |
| `.filtros`, `.filtros__opcion`, `.filtros__total` | Barra de buscar, ordenar y mostrar arriba de un listado (Docentes) |
| `.pasos`, `.paso`, `.paso__titulo`, `.paso__numero` | Formulario por pasos numerados (agregar clase al horario) |
| `.dias-botones` | Días como botones Lun–Vie en lugar de una lista |
| `.carga`, `.carga__fila` | "Horas del grupo": materia, barra y cuánto falta en un renglón |
| `.bloque__materia`, `.bloque__quien`, `.bloque__hora`, `.bloque--c0` … `.bloque--c7` | Cuadros del horario: tres renglones y un color suave por materia |
| `.tabla-captura`, `.tabla-captura__actual`, `.campo--calificacion` | Captura de calificaciones y faltas; la columna del parcial elegido va resaltada |
| `.tabla-calificaciones`, `.calif`, `.calif--baja`, `.calif--final` | "Mis calificaciones" del alumno; las reprobatorias en rojo |
| `.hoja-oficial`, `.formato`, `.formato__etiqueta`, `.firmas` | Boleta e informe con el formato oficial, listos para imprimir (una boleta por hoja) |
| `.error-pagina` | Páginas de error 403, 404, 419, 429 y 500 |

Las reglas para celular van al final de `estilos.css` (sección "Celular y tableta") para que ganen sobre las anteriores.
