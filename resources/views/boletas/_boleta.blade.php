{{--
    Una boleta, con la misma estructura de "BOLETA FORMATO PARCIAL" de la escuela:
    1) datos del plantel  2) datos del educando  3) calificaciones por UAC
    4) componente extendido, conducta y promedios  5) firmas y fecha de expedición.
    $boleta es un App\Calificaciones\Boleta (ahí se calculan promedios y letras).
--}}
@use('App\Calificaciones\Boleta')
@php
    $escuela = config('escolar.escuela');
    $alumno = $boleta->alumno;
    $parciales = range(1, config('escolar.parciales'));
    $marca = fn ($si) => $si ? 'X' : '';
@endphp

<section class="hoja-oficial boleta">
    {{-- 1. Datos del plantel --}}
    <table class="formato">
        <tr>
            <th rowspan="4" class="formato__dgb">{{ $escuela['direccion_general'] }}</th>
            <th colspan="4">Datos del plantel</th>
        </tr>
        <tr>
            <td class="formato__etiqueta">Nombre</td><td class="formato__etiqueta">Clave</td>
            <td class="formato__etiqueta">Modalidad</td><td class="formato__etiqueta">Turno</td>
        </tr>
        <tr>
            <td>"{{ mb_strtoupper($escuela['nombre']) }}"</td>
            <td>{{ $escuela['clave'] }}</td>
            <td>ESC <b>{{ $marca(true) }}</b> · MIX</td>
            <td>MAT · VESP <b>{{ $marca($escuela['turno'] === 'Vespertino') }}</b> · NOCT · DISC</td>
        </tr>
        <tr>
            <td class="formato__etiqueta">Localidad: {{ mb_strtoupper($escuela['municipio']) }}</td>
            <td class="formato__etiqueta" colspan="3">Municipio: {{ mb_strtoupper($escuela['municipio']) }}</td>
        </tr>
    </table>

    <p class="formato__titulo">Boleta interna y provisional de calificaciones</p>

    {{-- 2. Datos del educando --}}
    <table class="formato">
        <tr><th colspan="5">Datos del educando</th></tr>
        <tr>
            <td class="formato__etiqueta">Nombre</td><td class="formato__etiqueta">CURP</td>
            <td class="formato__etiqueta">Semestre</td><td class="formato__etiqueta">Grupo</td>
            <td class="formato__etiqueta">Periodo escolar</td>
        </tr>
        <tr>
            <td>{{ mb_strtoupper($alumno->name) }}</td>
            <td>{{ $alumno->curp ?? '—' }}</td>
            <td>{{ Boleta::romano($alumno->grupo->grado) }}</td>
            <td>{{ $alumno->grupo->letra }}</td>
            <td>{{ $boleta->semestre->nombre }}</td>
        </tr>
    </table>

    {{-- 3. Calificaciones por unidad de aprendizaje curricular --}}
    <table class="formato formato--boleta">
        <thead>
            <tr>
                <th rowspan="2">Unidades de aprendizaje curricular (UAC)</th>
                <th rowspan="2">Clases impartidas</th>
                <th rowspan="2">Faltas de asistencia</th>
                <th colspan="{{ count($parciales) }}">Calificaciones parciales</th>
                <th rowspan="2">Promedio por UAC</th>
                <th colspan="2">Calificación final</th>
                <th rowspan="2">Tipo de examen</th>
            </tr>
            <tr>
                @foreach ($parciales as $p) <th>{{ Boleta::romano($p) }}</th> @endforeach
                <th>No.</th><th>Letra</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($boleta->filas as $fila)
            <tr>
                <td class="formato__nombre">{{ mb_strtoupper($fila['materia']->nombre) }}</td>
                <td></td>
                <td></td>
                @foreach ($parciales as $p) <td>{{ $fila['parciales'][$p] ?? '-' }}</td> @endforeach
                <td>{{ $fila['final'] ?? '' }}</td>
                <td>{{ $fila['final_texto'] }}</td>
                <td class="formato__letra">{{ $fila['letra'] }}</td>
                <td>{{ $boleta->tipoExamen }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{-- 4. Componente extendido, conducta y promedios --}}
    <table class="formato">
        <tr>
            <th colspan="4">Comp. de for. fun. extendido</th>
            <th>Clave</th>
            <th colspan="3">Conducta</th>
            <th colspan="{{ count($parciales) }}">Promedio por parcial</th>
            <th colspan="2">Promedio general del semestre</th>
        </tr>
        <tr>
            <td>1</td><td>2</td><td>3</td><td>4</td>
            <td>CFLB</td>
            <td>Buena</td><td>Reg.</td><td>Mala</td>
            @foreach ($parciales as $p) <td>{{ Boleta::romano($p) }}</td> @endforeach
            <td>No.</td><td>Letra</td>
        </tr>
        <tr>
            <td></td><td></td><td></td><td></td>
            <td></td>
            <td></td><td></td><td></td>
            @foreach ($parciales as $p) <td>{{ $boleta->promedioParcial[$p] ?? '' }}</td> @endforeach
            <td>{{ $boleta->promedioGeneral ?? '' }}</td>
            <td class="formato__letra">{{ App\Calificaciones\Letra::de($boleta->promedioGeneral) }}</td>
        </tr>
    </table>

    {{-- 5. Firmas y fecha de expedición --}}
    <div class="boleta__pie">
        @include('boletas._firmas')
        <table class="formato formato--fecha">
            <tr><th colspan="3">Fecha de expedición</th></tr>
            <tr><td>Día</td><td>Mes</td><td>Año</td></tr>
            <tr><td>{{ now()->format('d') }}</td><td>{{ now()->format('m') }}</td><td>{{ now()->format('Y') }}</td></tr>
        </table>
    </div>
</section>
