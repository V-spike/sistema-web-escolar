@extends('layouts.app')

@section('titulo', 'Editar clase')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<div class="barra-catalogo">
    <div>
        <h1>{{ $clase->materia->nombre }}</h1>
        <p class="tenue" style="margin:0">Grupo {{ $clase->grupo->nombre }} · {{ config('escolar.dias')[$clase->dia] }} {{ $clase->rango }} · {{ $clase->docente->nombre_completo }}</p>
    </div>
    <form method="POST" action="{{ route('horarios.destroy', $clase) }}" onsubmit="return confirm('¿Quitar esta clase del horario?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn--peligro btn--chico">Quitar del horario</button>
    </form>
</div>

@include('horarios._revision')

@if (! session('empalmes') && ! $revision->sePuedeGuardar())
    <div class="alerta alerta--empalme">
        <h3>Esta clase tiene empalmes ahora mismo</h3>
        <ul>
            @foreach ($revision->empalmes as $problema)
                <li>{{ $problema['causa'] }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="panel" style="max-width:640px">
    @include('horarios._formulario', [
        'accion' => route('horarios.update', $clase),
        'metodo' => 'PUT',
        'grupo' => $clase->grupo,
        'clase' => $clase,
        'cancelar' => route('horarios.index', ['vista' => 'grupo', 'id' => $clase->grupo_id]),
    ])
</div>
@endsection
