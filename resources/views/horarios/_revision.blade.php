{{-- Empalmes (no se guardó) y avisos (sí se guardó) que regresó el servidor --}}
@if (session('empalmes'))
    <div class="alerta alerta--empalme" role="alert">
        <h3>No se guardó: la clase se empalma</h3>
        <ul>
            @foreach (session('empalmes') as $problema)
                <li>{{ $problema['causa'] }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('avisos'))
    <div class="alerta alerta--aviso">
        <h3>Avisos</h3>
        <ul>
            @foreach (session('avisos') as $problema)
                <li>{{ $problema['causa'] }}</li>
            @endforeach
        </ul>
    </div>
@endif
