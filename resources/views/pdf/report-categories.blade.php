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

<table border="1" width="100%" cellpadding="5" style="border-collapse: collapse;">
    <thead>
        <tr style="background: #eee;">
            <th>ID</th>
            <th>NAMA KATEGORI</th>
            <th>STATUS</th>
            <th>TOTAL PRODUK</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td>{{ strtoupper($item->name) }}</td>
            <td>{{ strtoupper($item->status) }}</td>
            <td>{{ $item->products->count() }} Item</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
