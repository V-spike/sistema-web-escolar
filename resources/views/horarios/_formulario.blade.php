{{--
    Formulario de una clase. Lo usan "Agregar clase" y "Editar clase".
    Va en tres pasos: 1) qué materia y quién la da  2) cuándo  3) dónde.
    Mientras se llena, revisa los empalmes en vivo con horarios.revisar; al guardar, el servidor vuelve a revisar.
    Recibe: $accion, $metodo, $grupo, $materias, $docentes, $aulas, $clase (opcional)
--}}
@php
    $clase = $clase ?? null;
    $dias = config('escolar.dias');
    $perfiles = $docentes->mapWithKeys(fn ($d) => [$d->id => $d->materias->pluck('id')->all()]);
    $diaElegido = (int) old('dia', $clase?->dia ?? 1);
@endphp
<form method="POST" action="{{ $accion }}" class="formulario formulario--clase pasos"
      data-revisar="{{ route('horarios.revisar') }}" data-ignorar="{{ $clase?->id }}">
    @csrf
    @if ($metodo !== 'POST') @method($metodo) @endif
    <input type="hidden" name="grupo_id" value="{{ $grupo->id }}">

    {{-- Paso 1 --}}
    <fieldset class="paso">
        <legend class="paso__titulo"><span class="paso__numero">1</span> Materia y docente</legend>

        <label for="materia_id" class="campo__etiqueta">Materia</label>
        <select id="materia_id" name="materia_id" class="campo @error('materia_id') campo--error @enderror" required>
            <option value="">Elige la materia…</option>
            @foreach ($materias as $materia)
                <option value="{{ $materia->id }}" @selected((int) old('materia_id', $clase?->materia_id) === $materia->id)>
                    {{ $materia->nombre }} ({{ $materia->horas_semana }} h)
                </option>
            @endforeach
        </select>
        @error('materia_id') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <label for="docente_id" class="campo__etiqueta">Docente</label>
        <select id="docente_id" name="docente_id" class="campo @error('docente_id') campo--error @enderror" required
                data-perfiles='@json($perfiles)'>
            <option value="">Elige el docente…</option>
            @foreach ($docentes as $docente)
                <option value="{{ $docente->id }}" @selected((int) old('docente_id', $clase?->docente_id) === $docente->id)>
                    {{ $docente->nombre_completo }} ({{ $docente->horas_plaza }} h)
                </option>
            @endforeach
        </select>
        <span class="campo__ayuda">Los que tienen la materia en su perfil salen primero, con ✓.</span>
        @error('docente_id') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
    </fieldset>

    {{-- Paso 2 --}}
    <fieldset class="paso">
        <legend class="paso__titulo"><span class="paso__numero">2</span> Día y hora</legend>

        <div class="dias-botones" role="radiogroup" aria-label="Día">
            @foreach ($dias as $numero => $nombre)
                <label @class(['dias-botones__opcion'])>
                    <input type="radio" name="dia" value="{{ $numero }}" @checked($diaElegido === $numero) required>
                    <span title="{{ $nombre }}">{{ mb_substr($nombre, 0, 3) }}</span>
                </label>
            @endforeach
        </div>
        @error('dia') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <div class="formulario__fila">
            <div>
                <label for="hora_inicio" class="campo__etiqueta">Empieza</label>
                <input id="hora_inicio" name="hora_inicio" type="time" step="300" required
                       value="{{ old('hora_inicio', $clase ? \App\Horarios\Hora::corta($clase->hora_inicio) : '') }}"
                       class="campo @error('hora_inicio') campo--error @enderror">
            </div>
            <div>
                <label for="hora_fin" class="campo__etiqueta">Termina</label>
                <input id="hora_fin" name="hora_fin" type="time" step="300" required
                       value="{{ old('hora_fin', $clase ? \App\Horarios\Hora::corta($clase->hora_fin) : '') }}"
                       class="campo @error('hora_fin') campo--error @enderror">
            </div>
        </div>
        @error('hora_inicio') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
        @error('hora_fin') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
    </fieldset>

    {{-- Paso 3 --}}
    <fieldset class="paso">
        <legend class="paso__titulo"><span class="paso__numero">3</span> Aula</legend>

        <select id="aula_id" name="aula_id" class="campo" aria-label="Aula">
            <option value="">Sin aula</option>
            @foreach ($aulas as $aula)
                <option value="{{ $aula->id }}" @selected((int) old('aula_id', $clase?->aula_id ?? $grupo->aula_id) === $aula->id)>
                    {{ $aula->clave }} · {{ $aula->edificio }} ({{ $aula->capacidad }})
                </option>
            @endforeach
        </select>
    </fieldset>

    {{-- Aquí aparece en vivo si la clase se encima con algo --}}
    <div class="revision-vivo" aria-live="polite"></div>

    <div class="formulario__acciones">
        <button type="submit" class="btn btn--primario">{{ $clase ? 'Guardar cambios' : 'Agregar clase' }}</button>
        @isset($cancelar)
            <a href="{{ $cancelar }}" class="btn btn--secundario btn--enlace">Cancelar</a>
        @endisset
    </div>
</form>

@once
@push('scripts')
<script>
/* Revisión de empalmes en vivo: cada cambio pregunta al servidor si la clase cabe.
   Es solo una ayuda: al guardar, el servidor vuelve a revisar todo. */
document.querySelectorAll('.formulario--clase').forEach(function (form) {
    const caja = form.querySelector('.revision-vivo');
    const materia = form.querySelector('#materia_id');
    const docente = form.querySelector('#docente_id');
    const perfiles = JSON.parse(docente.dataset.perfiles || '{}');
    const token = document.querySelector('meta[name="csrf-token"]').content;
    let espera;

    function escapar(texto) {
        const div = document.createElement('div');
        div.textContent = texto;
        return div.innerHTML;
    }

    // Ordena los docentes: primero los que tienen la materia en su perfil
    function ordenarDocentes() {
        const id = parseInt(materia.value, 10);
        const opciones = Array.from(docente.options).slice(1);
        opciones.forEach(function (op) {
            const puede = id && (perfiles[op.value] || []).includes(id);
            op.textContent = op.textContent.replace(/^✓ /, '');
            if (puede) op.textContent = '✓ ' + op.textContent;
            op.dataset.puede = puede ? '1' : '0';
        });
        opciones.sort(function (a, b) { return (b.dataset.puede - a.dataset.puede) || a.text.localeCompare(b.text); });
        opciones.forEach(function (op) { docente.appendChild(op); });
    }

    function pintar(r) {
        if (r.se_puede_guardar && !r.avisos.length) {
            caja.innerHTML = '<div class="alerta alerta--ok"><h3>Sin empalmes: se puede guardar</h3></div>';
            return;
        }
        let html = '';
        if (r.empalmes.length) {
            html += '<div class="alerta alerta--empalme"><h3>Se empalma, no se podrá guardar</h3><ul>' +
                r.empalmes.map(function (p) { return '<li>' + escapar(p.causa) + '</li>'; }).join('') + '</ul></div>';
        }
        if (r.avisos.length) {
            html += '<div class="alerta alerta--aviso"><h3>Avisos</h3><ul>' +
                r.avisos.map(function (p) { return '<li>' + escapar(p.causa) + '</li>'; }).join('') + '</ul></div>';
        }
        caja.innerHTML = html;
    }

    function revisar() {
        clearTimeout(espera);
        espera = setTimeout(function () {
            const datos = new FormData(form);
            datos.delete('_method');
            if (form.dataset.ignorar) datos.append('ignorar', form.dataset.ignorar);
            if (!datos.get('materia_id') || !datos.get('docente_id') || !datos.get('hora_inicio') || !datos.get('hora_fin')) {
                caja.innerHTML = '';
                return;
            }
            fetch(form.dataset.revisar, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: datos
            }).then(function (res) { return res.ok ? res.json() : null; })
              .then(function (r) { if (r) pintar(r); })
              .catch(function () { caja.innerHTML = ''; });
        }, 250);
    }

    materia.addEventListener('change', ordenarDocentes);
    form.addEventListener('change', revisar);
    form.addEventListener('input', function (e) { if (e.target.type === 'time') revisar(); });
    ordenarDocentes();
    revisar();
});
</script>
@endpush
@endonce
