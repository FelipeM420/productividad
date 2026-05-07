<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        body { margin: 20px; color: #1e293b; }
        h2   { font-size: 16px; margin-bottom: 4px; color: #1e1b4b; }
        .sub { color: #64748b; font-size: 10px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th    { background: #1e1b4b; color: #fff; padding: 7px 8px; text-align: center; font-size: 10px; }
        td    { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; }
        tr:nth-child(even) td { background: #f8fafc; }
        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .badge-success { background: #dcfce7; color: #166534; padding: 2px 7px; border-radius: 10px; }
        .badge-warning { background: #fef9c3; color: #854d0e; padding: 2px 7px; border-radius: 10px; }
        .badge-danger  { background: #fee2e2; color: #991b1b; padding: 2px 7px; border-radius: 10px; }
        .footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
    <h2>Sistema de Medición de Productividad</h2>
    <div class="sub">
        Reporte de Cumplimiento - {{ $meses[$mes] }} {{ $ano }} |
        Generado: {{ now()->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th colspan="4">Datos esperados del mes</th>
            </tr>
            <tr>
                <th>Metrica</th>
                <th>Real</th>
                <th>Esperado</th>
                <th>% esperado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proyeccion['metricas'] as $key => $m)
            <tr>
                <td><strong>{{ $m['label'] }}</strong></td>
                <td class="text-right">{{ $key === 'ventas' ? '$'.number_format($m['real'],0,',','.') : number_format($m['real'],0,',','.') }}</td>
                <td class="text-right">{{ $key === 'ventas' ? '$'.number_format($m['esperado'],0,',','.') : number_format($m['esperado'],0,',','.') }}</td>
                <td class="text-center">{{ $m['cumplimiento_esperado'] }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                <th>Vendedor</th>
                <th>Ventas Real</th>
                <th>Ventas Meta</th>
                <th>% V</th>
                <th>CA Real</th>
                <th>CA Meta</th>
                <th>% CA</th>
                <th>CV Real</th>
                <th>CV Meta</th>
                <th>% CV</th>
                <th>NC Real</th>
                <th>NC Meta</th>
                <th>% NC</th>
                <th>Global</th>
            </tr>
        </thead>
        <tbody>
            @php
                $badge = fn($p) => $p>=100 ? 'success' : ($p>=60 ? 'warning' : 'danger');
                $span  = fn($p) => "<span class='badge-{$badge($p)}'>{$p}%</span>";
            @endphp
            @forelse($reporte as $r)
            <tr>
                <td><strong>{{ $r['v']->name }}</strong></td>
                <td class="text-right">${{ number_format($r['rv'],0,',','.') }}</td>
                <td class="text-right">${{ number_format($r['mv'],0,',','.') }}</td>
                <td class="text-center">{!! $span($r['pv']) !!}</td>
                <td class="text-right">{{ $r['rca'] }}</td>
                <td class="text-right">{{ $r['mca'] }}</td>
                <td class="text-center">{!! $span($r['pca']) !!}</td>
                <td class="text-right">{{ $r['rcv'] }}</td>
                <td class="text-right">{{ $r['mcv'] }}</td>
                <td class="text-center">{!! $span($r['pcv']) !!}</td>
                <td class="text-right">{{ $r['rnc'] }}</td>
                <td class="text-right">{{ $r['mnc'] }}</td>
                <td class="text-center">{!! $span($r['pnc']) !!}</td>
                <td class="text-center"><strong>{!! $span($r['global']) !!}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="14" class="text-center">Sin datos para el período.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Sistema de Medición de Productividad — Generado automáticamente
    </div>
</body>
</html>
