{{-- Resumen del horario para administrador y superusuario --}}
<div class="bienvenida">
    <h1>Hola, {{ auth()->user()->name }}</h1>
    <p>{{ $semestre ? 'Semestre activo: ' . $semestre->nombre : 'No hay un semestre activo todavía.' }}</p>
</div>

<div class="kpis">
    <div class="kpi"><div class="kpi__valor">{{ $totales['grupos'] }}</div><div class="kpi__texto">Grupos activos</div></div>
    <div class="kpi"><div class="kpi__valor">{{ $totales['docentes'] }}</div><div class="kpi__texto">Docentes activos</div></div>
    <div class="kpi"><div class="kpi__valor">{{ $totales['clases'] }}</div><div class="kpi__texto">Clases acomodadas</div></div>
    <div @class(['kpi', 'kpi--alerta' => $empalmes > 0, 'kpi--ok' => $empalmes === 0])>
        <div class="kpi__valor">{{ $empalmes }}</div><div class="kpi__texto">Clases con empalme</div>
    </div>
    @isset($administradores)
        <div class="kpi"><div class="kpi__valor">{{ $administradores }}</div><div class="kpi__texto">Administradores</div></div>
    @endisset
</div>

<div class="panel">
    <div class="panel__titulo">
        <h2>Avance del horario por grupo</h2>
        <a href="{{ route('horarios.index') }}" class="btn btn--primario btn--chico btn--enlace">Acomodar horario</a>
    </div>
    <ul class="lista-simple carga">
        @forelse ($avance as $fila)
            <li>
                <a href="{{ route('horarios.index', ['vista' => 'grupo', 'id' => $fila['grupo']->id]) }}">{{ $fila['grupo']->nombre }}</a>
                <strong>{{ rtrim(rtrim(number_format($fila['asignadas'], 1), '0'), '.') }} / {{ $fila['requeridas'] }} h · {{ $fila['porcentaje'] }} %</strong>
                <div class="barra-progreso"><i style="width: {{ $fila['porcentaje'] }}%"></i></div>
            </li>
        @empty
            <li>Sin grupos en el semestre activo.</li>
        @endforelse
    </ul>
</div>
