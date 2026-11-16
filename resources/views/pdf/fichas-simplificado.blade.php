<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Fichas de treino</title>
<style>
    @font-face { font-family: 'Montserrat'; font-weight: 400; src: url('{{ $fonte }}/Montserrat-Regular.ttf') format('truetype'); }
    @font-face { font-family: 'Montserrat'; font-weight: 500; src: url('{{ $fonte }}/Montserrat-Medium.ttf') format('truetype'); }
    @font-face { font-family: 'Montserrat'; font-weight: 600; src: url('{{ $fonte }}/Montserrat-SemiBold.ttf') format('truetype'); }
    @font-face { font-family: 'Montserrat'; font-weight: 700; src: url('{{ $fonte }}/Montserrat-Bold.ttf') format('truetype'); }

    @page { margin: 12mm 12mm 16mm 12mm; }
    body { font-family: 'Montserrat', sans-serif; font-size: 8.5pt; color: #2B2D31; margin: 0; }
    table { border-collapse: collapse; width: 100%; }
    td, th { vertical-align: top; padding: 0; }

    .rodape { position: fixed; bottom: -10mm; left: 0; right: 0; height: 6mm; font-size: 7.5pt; color: #8A8D93; }
    .rodape b { color: #0E0F11; font-weight: 700; letter-spacing: .6pt; }

    /* topo do documento (uma vez só) */
    .topo { background: #0E0F11; color: #fff; border-radius: 3mm; }
    .topo td { padding: 4mm 5mm; vertical-align: middle; }
    .marca { color: #E4FF3A; font-size: 7pt; font-weight: 700; letter-spacing: 2pt; text-transform: uppercase; }
    .nome { font-size: 15pt; font-weight: 700; margin-top: 1mm; }
    .info { color: #B9BCC2; font-size: 8pt; text-align: right; }
    .info b { color: #E4FF3A; font-size: 13pt; font-weight: 700; }

    /* uma ficha */
    .ficha { margin-top: 6mm; }
    .ficha.inteira { page-break-inside: avoid; }
    .cab td { vertical-align: bottom; padding-bottom: 1.5mm; }
    .titulo { font-size: 11.5pt; font-weight: 700; color: #0E0F11; border-left: 1.6mm solid #E4FF3A; padding-left: 2.5mm; }
    .qtd { text-align: right; font-size: 7.5pt; color: #8A8D93; font-weight: 600; letter-spacing: .5pt; text-transform: uppercase; }
    .orientacoes { font-size: 8pt; color: #55585E; margin: 0 0 2mm 4.1mm; line-height: 1.4; }

    .ex thead th { background: #0E0F11; color: #fff; font-size: 6.5pt; font-weight: 700; letter-spacing: 1pt; text-transform: uppercase; text-align: left; padding: 1.6mm 2mm; }
    .ex thead th.c { text-align: center; }
    .ex tbody td { padding: 1.6mm 2mm; border-bottom: .25mm solid #E4E4DE; }
    .ex tbody tr:nth-child(even) td { background: #F7F7F3; }
    .ex tbody tr { page-break-inside: avoid; }
    .n { width: 6mm; color: #8A8D93; font-weight: 600; }
    .exe b { font-weight: 600; color: #0E0F11; }
    .exe span { color: #8A8D93; font-size: 7pt; }
    .c { text-align: center; white-space: nowrap; }
    .sr { width: 13mm; }
    .rp { width: 16mm; font-weight: 700; color: #0E0F11; }
    .cg { width: 15mm; }
    .ds { width: 16mm; }
    .obs { width: 52mm; color: #55585E; font-size: 7.5pt; line-height: 1.35; }
    .vazia { color: #8A8D93; margin-left: 4.1mm; }
</style>
</head>
<body>
<div class="rodape">
    <table><tr>
        <td><b>PERSONAL DEF</b> &nbsp;·&nbsp; personaldef.com.br</td>
        <td style="text-align: right; padding-right: 22mm">gerado em {{ $geradoEm }}</td>
    </tr></table>
</div>

<table class="topo"><tr>
    <td>
        <div class="marca">Personal DEF · Fichas de treino</div>
        <div class="nome">{{ $aluno ?: (count($fichas) === 1 ? $fichas[0]['titulo'] : 'Fichas de treino') }}</div>
    </td>
    <td class="info" style="width: 40mm">
        <b>{{ count($fichas) }}</b> ficha{{ count($fichas) === 1 ? '' : 's' }}<br>
        {{ collect($fichas)->sum(fn ($f) => count($f['exercicios'])) }} exercícios
    </td>
</tr></table>

@foreach ($fichas as $f)
    @php
        $ex = $f['exercicios'];
        $temCarga = collect($ex)->contains(fn ($e) => ! empty($e['detalhes']['Carga']));
        $temDescanso = collect($ex)->contains(fn ($e) => ! empty($e['detalhes']['Descanso']));
        $obs = fn ($e) => implode(' · ', array_filter([
            $e['detalhes']['Método'] ?? null,
            isset($e['detalhes']['Cadência']) ? 'cadência '.$e['detalhes']['Cadência'] : null,
            isset($e['detalhes']['Tempo']) ? 'tempo '.$e['detalhes']['Tempo'] : null,
            $e['obs'],
        ]));
        $temObs = collect($ex)->contains(fn ($e) => $obs($e) !== '');
    @endphp
    <div class="ficha {{ count($ex) <= 18 ? 'inteira' : '' }}">
        <table class="cab"><tr>
            <td><div class="titulo">{{ $f['titulo'] }}</div></td>
            <td class="qtd" style="width: 40mm">{{ count($ex) }} exercício{{ count($ex) === 1 ? '' : 's' }}</td>
        </tr></table>
        @if ($f['orientacoes'])<div class="orientacoes">{!! nl2br(e($f['orientacoes'])) !!}</div>@endif

        @if ($ex)
            <table class="ex">
                <thead><tr>
                    <th class="n">#</th>
                    <th>Exercício</th>
                    <th class="c sr">Séries</th>
                    <th class="c rp">Reps</th>
                    @if ($temCarga)<th class="c cg">Carga</th>@endif
                    @if ($temDescanso)<th class="c ds">Descanso</th>@endif
                    @if ($temObs)<th class="obs">Observações</th>@endif
                </tr></thead>
                <tbody>
                @foreach ($ex as $i => $e)
                    <tr>
                        <td class="n">{{ $i + 1 }}</td>
                        <td class="exe"><b>{{ $e['nome'] }}</b>@if ($e['musculo'])<br><span>{{ $e['musculo'] }}</span>@endif</td>
                        <td class="c sr">{{ $e['series'] ?: '—' }}</td>
                        <td class="c rp">{{ $e['repeticoes'] ?: '—' }}</td>
                        @if ($temCarga)<td class="c cg">{{ $e['detalhes']['Carga'] ?? '' }}</td>@endif
                        @if ($temDescanso)<td class="c ds">{{ $e['detalhes']['Descanso'] ?? '' }}</td>@endif
                        @if ($temObs)<td class="obs">{{ $obs($e) }}</td>@endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <p class="vazia">Esta ficha ainda não tem exercícios.</p>
        @endif
    </div>
@endforeach
</body>
</html>
