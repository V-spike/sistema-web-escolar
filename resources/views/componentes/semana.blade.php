{{--
    Semana de lunes a viernes con cada bloque puesto según su hora real.
    Recibe: $semana (App\Horarios\Semana) y opcional $enlazar (true = cada clase abre su edición).
--}}
@php
    $enlazar = $enlazar ?? false;
    $chica = $chica ?? false;
@endphp
<div @class(['semana', 'semana--chica' => $chica]) style="--alto: {{ $chica ? (int) ($semana->altoPx() * .75) : $semana->altoPx() }}px; --paso: {{ round(30 * 100 / max(1, $semana->salida - $semana->entrada), 3) }}%">
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
                    $titulo = $b['nombre'] . ' · ' . $nombre . ' ' . $b['rango'] . ($b['lineas'] ? ' · ' . implode(' · ', $b['lineas']) : '');
                @endphp
                @if ($enlazar && $b['id'] && in_array($b['tipo'], ['clase', 'problema']))
                    <a href="{{ route('horarios.edit', $b['id']) }}" @class($clases) style="{{ $estilo }}" title="{{ $titulo }}">
                        <b>{{ $b['titulo'] }}</b>
                        <span>{{ $b['rango'] }}</span>
                        @foreach ($b['lineas'] as $linea) <span>{{ $linea }}</span> @endforeach
                    </a>
                @else
                    <div @class($clases) style="{{ $estilo }}" title="{{ $titulo }}">
                        <b>{{ $b['titulo'] }}</b>
                        <span>{{ $b['rango'] }}</span>
                        @foreach ($b['lineas'] as $linea) <span>{{ $linea }}</span> @endforeach
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach
</div>
<div class="leyenda">
    <span><i style="background:#EEF2FF;border-color:#6366F1"></i>Clase</span>
    <span><i style="background:#EDE9FE;border-color:#7C3AED"></i>Bloque común</span>
    <span><i style="background:#E2E8F0"></i>Receso</span>
    <span><i style="background:#FEE2E2;border-color:#B91C1C"></i>Con empalme</span>
</div>
