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

    @page { margin: 14mm 13mm 17mm 13mm; }
    * { box-sizing: border-box; }
    body { font-family: 'Montserrat', sans-serif; font-size: 9.5pt; color: #2B2D31; margin: 0; }
    table { border-collapse: collapse; width: 100%; }
    td { vertical-align: top; padding: 0; }

    /* rodapé em todas as páginas */
    .rodape { position: fixed; bottom: -10mm; left: 0; right: 0; height: 6mm; font-size: 7.5pt; color: #8A8D93; }
    .rodape b { color: #0E0F11; font-weight: 700; letter-spacing: .6pt; }

    .ficha { page-break-after: always; }
    .ficha.ultima { page-break-after: auto; }

    /* topo preto da ficha */
    .topo { background: #0E0F11; color: #fff; border-radius: 5mm; }
    .topo td { padding: 6mm 7mm; vertical-align: middle; }
    .marca { color: #E4FF3A; font-size: 7.5pt; font-weight: 700; letter-spacing: 2pt; text-transform: uppercase; }
    .titulo-ficha { font-size: 21pt; font-weight: 700; line-height: 1.15; margin-top: 1.5mm; letter-spacing: -.3pt; }
    .sub { color: #B9BCC2; font-size: 9pt; margin-top: 1.5mm; }
    .sub b { color: #fff; font-weight: 600; }
    .contagem { background: #E4FF3A; color: #0E0F11; border-radius: 4mm; text-align: center; width: 27mm; padding: 3mm 0; }
    .contagem .n { font-size: 22pt; font-weight: 700; line-height: 1; }
    .contagem .l { font-size: 7pt; font-weight: 600; letter-spacing: 1pt; text-transform: uppercase; margin-top: 1mm; }

    .orientacoes { margin-top: 4mm; background: #F4F4EF; border-left: 1.6mm solid #E4FF3A; border-radius: 2mm; padding: 3.5mm 5mm; font-size: 9pt; line-height: 1.45; }
    .rotulo { font-size: 7pt; font-weight: 700; letter-spacing: 1.4pt; text-transform: uppercase; color: #55585E; margin-bottom: 1mm; }

    /* cartão do exercício */
    .ex { margin-top: 4mm; border: .3mm solid #E4E4DE; border-radius: 4mm; page-break-inside: avoid; background: #fff; }
    .ex > tbody > tr > td, .ex > tr > td { padding: 3.5mm; vertical-align: middle; }
    .num { width: 11mm; text-align: center; }
    .num div { width: 9mm; height: 6.7mm; padding-top: 2.3mm; border-radius: 4.5mm; background: #0E0F11; color: #E4FF3A; font-weight: 700; font-size: 9pt; line-height: 1; text-align: center; }
    .fotos { width: 76mm; padding-left: 0 !important; }
    .quadro { width: 36mm; height: 36mm; border: .3mm solid #ECECE6; border-radius: 2.5mm; }
    .vao { width: 3mm; }
    .sem-foto { width: 36mm; height: 36mm; border-radius: 2.5mm; background: #F4F4EF; color: #A3A6AB; text-align: center; font-size: 7.5pt; line-height: 36mm; }
    .nome { font-size: 12.5pt; font-weight: 700; color: #0E0F11; line-height: 1.2; }
    .musculo { font-size: 7pt; font-weight: 700; letter-spacing: 1.3pt; text-transform: uppercase; color: #8A8D93; margin-top: 1mm; }
    .prescricao { margin-top: 2.5mm; }
    .series { display: inline-block; background: #0E0F11; color: #E4FF3A; border-radius: 2mm; padding: 1.5mm 3mm; font-size: 12pt; font-weight: 700; }
    .series small { font-size: 7pt; font-weight: 600; color: #fff; letter-spacing: .8pt; text-transform: uppercase; }
    .det { margin-top: 2.5mm; }
    .det span { display: inline-block; border: .3mm solid #E4E4DE; border-radius: 2mm; padding: 1mm 2.2mm; margin: 0 1.2mm 1.2mm 0; font-size: 8pt; }
    .det span i { font-style: normal; color: #8A8D93; font-size: 6.5pt; font-weight: 700; letter-spacing: .8pt; text-transform: uppercase; }
    .obs { margin-top: 1.5mm; font-size: 8.5pt; color: #55585E; line-height: 1.4; }
    .vazia { margin-top: 8mm; color: #8A8D93; }
</style>
</head>
<body>
<div class="rodape">
    <table><tr>
        <td><b>PERSONAL DEF</b> &nbsp;·&nbsp; personaldef.com.br</td>
        <td style="text-align: right; padding-right: 22mm">gerado em {{ $geradoEm }}</td>
    </tr></table>
</div>

@foreach ($fichas as $f)
<div class="ficha {{ $loop->last ? 'ultima' : '' }}">
    <table class="topo"><tr>
        <td>
            <div class="marca">Personal DEF · Ficha de treino</div>
            <div class="titulo-ficha">{{ $f['titulo'] }}</div>
            <div class="sub">
                @if ($aluno)<b>{{ $aluno }}</b> &nbsp;·&nbsp; @endif
                criada em {{ $f['data'] }}
            </div>
        </td>
        <td style="width: 34mm; text-align: right">
            <div class="contagem"><div class="n">{{ count($f['exercicios']) }}</div><div class="l">exercício{{ count($f['exercicios']) === 1 ? '' : 's' }}</div></div>
        </td>
    </tr></table>

    @if ($f['orientacoes'])
        <div class="orientacoes"><div class="rotulo">Orientações</div>{!! nl2br(e($f['orientacoes'])) !!}</div>
    @endif

    @forelse ($f['exercicios'] as $i => $e)
        <table class="ex" @if ($e['cor']) style="border-left: 1.8mm solid {{ $e['cor'] }}" @endif><tr>
            <td class="num"><div>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div></td>
            <td class="fotos">
                <table style="width: auto"><tr>
                    @forelse ($e['quadros'] as $q)
                        @if (! $loop->first)<td class="vao"></td>@endif
                        <td><img class="quadro" src="{{ $q }}"></td>
                    @empty
                        <td><div class="sem-foto">sem imagem</div></td>
                    @endforelse
                </tr></table>
            </td>
            <td>
                <div class="nome">{{ $e['nome'] }}</div>
                @if ($e['musculo'])<div class="musculo">{{ $e['musculo'] }}</div>@endif
                @if ($e['series'] || $e['repeticoes'])
                    <div class="prescricao">
                        <span class="series">
                            @if ($e['series']){{ $e['series'] }} <small>séries</small>@endif
                            @if ($e['series'] && $e['repeticoes']) &nbsp;×&nbsp; @endif
                            @if ($e['repeticoes']){{ $e['repeticoes'] }} <small>reps</small>@endif
                        </span>
                    </div>
                @endif
                @if ($e['detalhes'])
                    <div class="det">@foreach ($e['detalhes'] as $rotulo => $valor)<span><i>{{ $rotulo }}</i> {{ $valor }}</span>@endforeach</div>
                @endif
                @if ($e['obs'])<div class="obs">{!! nl2br(e($e['obs'])) !!}</div>@endif
            </td>
        </tr></table>
    @empty
        <p class="vazia">Esta ficha ainda não tem exercícios.</p>
    @endforelse
</div>
@endforeach
</body>
</html>
