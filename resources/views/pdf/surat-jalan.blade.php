@php
if (!isset($order) && isset($rfq)) {
$order = $rfq;
}
@endphp

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

        .header-table,
        .info-table,
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
        }

        .data-table th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .data-table td {
            padding: 8px;
            vertical-align: top;
            border-bottom: 0.5px dotted #ccc;
        }

        .signature-table {
            width: 100%;
            margin-top: 30px;
        }

        .space {
            height: 70px;
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
                <span class="title">SURAT JALAN</span>
            </td>
        </tr>
    </table>

    <table class="info-table" style="margin-top: 20px;">
        <tr>
            <td width="55%" style="border: 1px solid #000; padding: 10px;">
                <small>Kepada Yth.</small><br>
                <strong>
                    {{-- Menggunakan optional untuk mencegah error null --}}
                    {{ strtoupper(optional($order->user)->name ?? 'Pelanggan') }}
                </strong><br>
                <div style="margin-top:5px;">
                    {{ $order->shipping_address ?? (optional($order->user)->address ?? '-') }}
                </div>
            </td>
            <td width="45%" style="padding-left: 20px; vertical-align: top;">
                <table width="100%">
                    <tr>
                        <td width="40%">No. SJ</td>
                        <td>: {{ $order->shipping_reference ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>: {{ now()->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td>Nama Kurir</td>
                        <td>: {{ strtoupper($order->courier_name ?? 'Internal') }}</td>
                    </tr>
                    <tr>
                        <td>Ekspedisi</td>
                        <td>: {{ $order->shipping_option === 'internal' ? 'Kurir Internal' : 'Ekspedisi Luar' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="data-table" style="margin-top: 20px;">
        <thead>
            <tr>
                <th width="15%">QTY</th>
                <th width="55%">DESKRIPSI BARANG</th>
                <th width="30%" class="text-right">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($order->specification))
            <tr>
                <td>{{ $order->quantity }} Pcs</td>
                <td>
                    <strong>{{ $order->product->name }}</strong><br>
                    <small>Spec: {{ $order->specification }}</small>
                </td>
                <td class="text-right">
                    {{-- Menggunakan final_price untuk RFQ --}}
                    Rp {{ number_format($order->final_price, 0, ',', '.') }}
                </td>
            </tr>
            @else
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->quantity }} Pcs</td>
                <td>{{ $item->product->name }}</td>
                <td class="text-right">
                    {{-- Menggunakan base_price x quantity untuk subtotal item --}}
                    Rp {{ number_format($item->quantity * $item->product->base_price, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right" style="padding-top:10px;"><strong>TOTAL AKHIR:</strong></td>
                <td class="text-right" style="padding-top:10px; border-bottom: 2px solid #000;">
                    <strong>
                        @if(isset($order->specification))
                        {{-- Total untuk RFQ --}}
                        Rp {{ number_format($order->final_price, 0, ',', '.') }}
                        @else
                        {{-- Total untuk Order Biasa --}}
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                        @endif
                    </strong>
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- <table class="signature-table">
        <tr>
            <td class="text-center">
                Tanda Terima,<br><br>
                <div class="space"></div>
                ( ........................ )
            </td>
            <td class="text-center">
                Hormat Kami,<br><br>
                <div class="space"></div>
                ( <strong>ADMIN GUDANG</strong> )
            </td>
        </tr>
    </table> --}}
</body>

</html>