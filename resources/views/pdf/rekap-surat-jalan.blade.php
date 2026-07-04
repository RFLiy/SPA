<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Courier', sans-serif;
            font-size: 12px;
            color: #000;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .title {
            font-size: 20px;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .data-table th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 8px 5px;
            text-align: center;
            background-color: #fff;
        }
        .data-table td {
            padding: 8px 5px;
            vertical-align: top;
            border-bottom: 0.5px dotted #ccc;
        }
        .footer-info {
            margin-top: 30px;
            font-size: 10px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .summary-box {
            margin-top: 20px;
            float: right;
            width: 250px;
            border: 1px solid #000;
            padding: 10px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="70%">
                <table width="100%">
                    <tr>
                        <td width="60px">
                            @if(file_exists(public_path('images/lgo.png')))
                                <img src="{{ public_path('images/lgo.png') }}" style="width: 50px; height: auto;">
                            @else
                                <div style="width: 50px; height: 50px; background: #eee; display: inline-block;"></div>
                            @endif
                        </td>
                        <td style="vertical-align: middle; padding-left: 10px;">
                            <strong style="font-size: 16px;">PT SINAR PERKASA ABADI</strong><br>
                            <small>Penyedia Leaf Spring & Komponen Otomotif</small>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="text-right" style="vertical-align: middle;">
                <span class="title">REKAP SURAT JALAN</span>
            </td>
        </tr>
    </table>

    <table width="100%" style="margin-top: 10px;">
        <tr>
            <td><small>Tanggal Cetak: {{ now()->format('d F Y H:i') }}</small></td>
            <td class="text-right"><small>Dicetak Oleh: {{ auth()->user()->name }}</small></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="15%">NO. REF/SJ</th>
                <th width="20%">CUSTOMER</th>
                <th width="40%">NAMA BARANG & SPESIFIKASI</th>
                <th width="10%">QTY</th>
                <th width="10%">KURIR</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $rfq)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center"><strong>{{ $rfq->shipping_reference ?? $rfq->order_code }}</strong></td>
                <td>{{ strtoupper($rfq->user->name ?? '-') }}</td>
                <td>
                    @if(isset($rfq->items))
                        @foreach($rfq->items as $item)
                            - {{ $item->product->name }}<br>
                        @endforeach
                    @else
                        <strong>{{ $rfq->product->name ?? '-' }}</strong><br>
                        <small style="font-size: 10px;">{{ $rfq->specification }}</small>
                    @endif
                </td>
                <td class="text-center">
                    {{ isset($rfq->items) ? $rfq->items->sum('quantity') : $rfq->quantity }}
                </td>
                <td class="text-center">{{ $rfq->courier_name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <table width="100%">
            <tr>
                <td>Total Pengiriman</td>
                <td>: {{ count($records) }} SJ</td>
            </tr>
            <tr>
                <td><strong>Total Barang</strong></td>
                <td>: <strong>
                    {{ $records->sum(function($r) {
                        return isset($r->items) ? $r->items->sum('quantity') : $r->quantity;
                    }) }} Pcs
                </strong></td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <div class="footer-info">
        <small><i>Catatan: Dokumen ini adalah rekapitulasi resmi dari sistem internal PT SINAR PERKASA ABADI.</i></small>
    </div>
</body>
</html>
