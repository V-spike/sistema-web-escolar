# Sistema web escolar

Sistema para armar el horario de una escuela sin empalmes. El administrador captura los catálogos (semestres, jornada, materias, aulas, docentes y grupos), acomoda las clases a mano y el sistema le avisa al momento si algo se encima. Docentes y alumnos entran a ver su horario; los docentes capturan calificaciones por parcial y los alumnos ven su boleta.

Hecho con Laravel 13, PHP 8.3 o más nuevo y MySQL. Las vistas son Blade con una sola hoja de estilos (`public/css/estilos.css`), sin Vite ni frameworks de CSS.

## Instalación

```
composer install
copy .env.example .env
php artisan key:generate
```

En `.env` pon el usuario y la contraseña de MySQL (`DB_USERNAME`, `DB_PASSWORD`) y crea la base de datos `escolar`. Luego:

```
php artisan migrate:fresh --seed
php artisan serve
```

Abre http://127.0.0.1:8000. El seeder deja datos de ejemplo: un semestre activo, la jornada con recesos, materias, aulas, 92 docentes, materias de 1.º, 3.º y 5.º (las de 3.º y 5.º son de ejemplo), 13 grupos (1.º A–G, 3.º A–C y 5.º A–C) con 15 alumnos inventados cada uno (menos 1.º G) y calificaciones de ejemplo. El horario queda vacío para acomodarlo a mano.

Los nombres que firman la boleta van en `.env` (`ESCUELA_DIRECTOR` y `ESCUELA_SUBDIRECTOR`).

Para que funcione "¿Olvidaste tu contraseña?" llena los datos de correo (`MAIL_USERNAME`, `MAIL_PASSWORD` con una contraseña de aplicación de Gmail y `MAIL_FROM_ADDRESS`).

## Usuarios de prueba

Todos usan la contraseña `Escolar2026`.

| Correo | Rol | Qué ve |
|---|---|---|
| superusuario@escolar.test | Superusuario | Todo, más el alta de administradores |
| administrador@escolar.test | Administrador | Catálogos, horario, calificaciones y boletas |
| docente@escolar.test | Docente | Su horario y la captura de calificaciones (ligado al docente D-001) |
| alumno@escolar.test | Alumno | Su horario y su boleta (1.º A) |
| alumno.a01@escolar.test … alumno.f15@escolar.test (1.º), alumno.3a01@… y alumno.5a01@… (3.º y 5.º) | Alumno | 15 alumnos inventados por grupo, con calificaciones de ejemplo |
| baja@escolar.test | Alumno dado de baja | No puede entrar |

## Pruebas

```
php artisan test
```

Las pruebas están en `tests/Feature/`, un archivo por módulo. `RecorridoTest` entra con cada rol a todas las pantallas para comprobar que ninguna falla. Usan su propia base de datos en memoria, así que no tocan tus datos.

## Estructura

```
app/
  Http/Controllers/   Un controlador por pantalla o catálogo
  Http/Middleware/    VerificarRol (quién entra a cada ruta), CuentaActiva, SinCache
  Models/             Tablas de la base de datos
  Horarios/           La lógica del horario
    RevisorEmpalmes.php   Revisa si una clase se encima con algo y explica la causa
    Guardas.php           Impide bajas o cambios que dejarían clases sin sentido
    Semana.php            Arma la cuadrícula de la semana para las vistas
    Hora.php              Convierte "15:50" a minutos y de regreso
  Calificaciones/     La boleta
    Boleta.php            Arma la boleta de un alumno: parciales, promedios y calificación final
    Letra.php             Escribe la calificación con letra (8.5 → OCHO PUNTO CINCO)
config/escolar.php    Datos fijos (nombres de los días)
database/
  migrations/         Estructura de las tablas
  seeders/            Datos de ejemplo
resources/views/
  layouts/            Plantillas: app (con sesión) e invitado (login)
  panel/              Inicio de cada rol y menú lateral
  acceso/             Login y recuperar contraseña
  errors/             Páginas 403, 404, 419, 429 y 500
  <catálogo>/         index, crear y editar de cada catálogo
routes/web.php        Todas las rutas, agrupadas por rol
docs/                 Diagramas, wireframes, guía de estilo y preguntas al profesor
_to_delete/           Archivos que ya no se usan, con su registro
```

## Roles

- **Superusuario**: todo lo del administrador y además da de alta administradores.
- **Administrador**: catálogos, horario, docentes (con su cuenta para entrar), grupos, alumnos, calificaciones, conducta, informes por grupo (imprimir o descargar para Excel) y boletas.
- **Docente**: ve su horario, captura por parcial las calificaciones, faltas y clases impartidas de los grupos donde da clase, la conducta si es tutor del grupo, e imprime sus boletas.
- **Alumno**: ve su horario y "Mis calificaciones" (cada materia con sus parciales, la final y sus faltas).

Nada se borra de los catálogos: se da de baja y se puede reactivar. El sistema no deja dar de baja algo que todavía tiene clases en el horario.
