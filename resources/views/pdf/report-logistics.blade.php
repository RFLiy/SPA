<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Courier', sans-serif; font-size: 11px; line-height: 1.4; }
        .header-table { width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .data-table th { border: 1px solid #000; padding: 8px; background-color: #f2f2f2; text-align: left; }
        .data-table td { border: 1px solid #000; padding: 8px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </table>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="70%">
                <strong style="font-size: 16px;">PT SINAR PERKASA ABADI</strong><br>
                <small>Laporan Inventaris & Master Data Produk</small>
            </td>
            <td class="text-right">
                <span style="font-size: 16px; font-weight: bold;">{{ $title }}</span><br>
                <small>Tgl: {{ date('d/m/Y') }}</small>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">NO</th>
                @if($type == 'kategori')
                    <th>NAMA KATEGORI</th>
                    <th>DESKRIPSI</th>
                    <th class="text-center">JUMLAH ITEM</th>
                @else
                    <th>NAMA BARANG</th>
                    <th>KATEGORI</th>
                    <th class="text-center">STOK</th>
                    <th class="text-center">SATUAN</th>
                    <th class="text-right">HARGA DASAR</th>
                @endif
            </tr>
        </thead>

        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>

                @if($type == 'kategori')
                    {{-- Tampilan Khusus Kategori --}}
                    <td>{{ strtoupper($item->name) }}</td>
                    <td>{{ $item->description ?? '-' }}</td>
                    <td class="text-center">{{ $item->products_count ?? $item->products->count() }} Item</td>

                @else
                    {{-- Tampilan Produk / Bahan Baku --}}
                    <td>{{ strtoupper($item->name) }}</td>
                    <td>{{ strtoupper($item->category->name ?? 'TANPA KATEGORI') }}</td>
                    <td class="text-center" style="{{ $item->stock <= 5 ? 'color: red; font-weight: bold;' : '' }}">
                        {{ $item->stock }}
                    </td>
                    <td class="text-center">{{ $item->unit ?? 'Pcs' }}</td>
                    <td class="text-right">Rp {{ number_format($item->base_price, 0, ',', '.') }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
