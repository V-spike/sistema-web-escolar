{{--
    Semana de lunes a viernes con cada clase puesta según su hora real.
    Recibe: $semana (App\Horarios\Semana) y opcional $enlazar (true = cada clase abre su edición).
    Cada cuadro muestra: la materia, quién la da (o el grupo) y abajo la hora y el aula.
--}}
@php
    $enlazar = $enlazar ?? false;
    $chica = $chica ?? false;
@endphp
<div @class(['semana', 'semana--chica' => $chica]) style="--alto: {{ $chica ? (int) ($semana->altoPx() * .75) : $semana->altoPx() }}px; --paso: {{ round(60 * 100 / max(1, $semana->salida - $semana->entrada), 3) }}%">
    <div class="semana__cabeza"></div>
    @foreach ($semana->dias as $nombre)
        <div class="semana__cabeza">{{ $nombre }}</div>
    @endforeach

    <div class="semana__regla" aria-hidden="true">
        @foreach ($semana->marcas as $marca)
            <span class="semana__marca" style="top: {{ $marca['arriba'] }}%">{{ $marca['texto'] }}</span>
        @endforeach
    </div>

    @foreach ($semana->dias as $numero => $nombre)
        <div class="semana__dia" aria-label="{{ $nombre }}">
            @foreach ($semana->recesos as $receso)
                <div class="semana__receso" style="top: {{ $receso['arriba'] }}%; height: {{ $receso['alto'] }}%">{{ $receso['texto'] }}</div>
            @endforeach

            @foreach ($semana->bloques[$numero] as $b)
                @php
                    $clases = ['bloque', 'bloque--' . $b['tipo'], 'bloque--c' . $b['color'] => $b['tipo'] === 'clase'];
                    $estilo = "top: {$b['arriba']}%; height: {$b['alto']}%";
                    $titulo = $b['nombre'] . ' · ' . $nombre . ' ' . $b['rango'] . ($b['lineas'] ? ' · ' . implode(' · ', array_filter($b['lineas'])) : '');
                    $quien = $b['lineas'][0] ?? '';
                    $abajo = trim($b['rango'] . (! empty($b['lineas'][1]) ? ' · ' . $b['lineas'][1] : ''));
                    $etiqueta = ($enlazar && $b['id'] && in_array($b['tipo'], ['clase', 'problema'])) ? 'a' : 'div';
                @endphp
                <{{ $etiqueta }} @if ($etiqueta === 'a') href="{{ route('horarios.edit', $b['id']) }}" @endif
                    @class($clases) style="{{ $estilo }}" title="{{ $titulo }}">
                    <b class="bloque__materia">{{ $b['titulo'] }}</b>
                    @if ($quien) <span class="bloque__quien">{{ $quien }}</span> @endif
                    <span class="bloque__hora">{{ $abajo }}</span>
                </{{ $etiqueta }}>
            @endforeach
        </div>
    @endforeach
</div>
<div class="leyenda">
    <span><i class="leyenda__clase"></i>Clase (el color depende de la materia)</span>
    <span><i class="leyenda__receso"></i>Receso</span>
    <span><i class="leyenda__problema"></i>Con empalme</span>
</div>
