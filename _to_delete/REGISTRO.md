# Archivos por borrar

Aquí van archivos que parece que ya no se usan. No se borran de golpe: se revisan y, si nadie los necesita, se eliminan después de la entrega.

| Fecha | Archivo | De dónde salió | Por qué |
|---|---|---|---|
| 2026-11-26 | `resources/views/welcome.blade.php` | `resources/views/` | Es la página de bienvenida de Laravel. Ninguna ruta la usa: `/` manda a `/login`. |
| 2026-11-26 | `app/Http/Controllers/BloqueComunController.php`, `app/Models/BloqueComun.php`, `resources/views/bloques-comunes/`, `database/seeders/BloqueComunSeeder.php`, `tests/Feature/BloqueComunTest.php` | Sus mismas carpetas | La escuela no usa bloques comunes (Yuu, 26/11). Currículum Ampliado ahora se acomoda como clase normal en cada grupo. La tabla `bloques_comunes` la quita la migración `2026_11_27_000004_quitar_bloques_comunes`. |
| 2026-11-26 | `database/seeders/HorarioDemoSeeder.php` | `database/seeders/` | Yuu acomoda el horario a mano: ya no se llena solo al sembrar. La tutoría en 7 perfiles pasó a `datos/docentes.csv` y la cuenta docente@escolar.test a `DocentesSeeder`. |
