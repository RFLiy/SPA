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
        .title { font-size: 16px; font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="70%">
                <strong style="font-size: 16px;">PT SINAR PERKASA ABADI</strong><br>
                <small>Laporan Manajemen Data Akun Pengguna</small>
            </td>
            <td class="text-right">
                <span class="title">{{ $title }}</span><br>
                <small>Tgl: {{ date('d/m/Y') }}</small>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th>NAMA LENGKAP</th>
                <th>EMAIL</th>
                <th>JABATAN (ROLE)</th>
                <th>TANGGAL DAFTAR</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $user)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ strtoupper($user->name) }}</td>
                <td>{{ $user->email }}</td>
                <td class="text-center">
                    @php
                        $roleDariShield = $user->roles->first()?->name;
                        $roleFinal = $roleDariShield ?? $user->role;
                        $roleFinal = strtoupper($roleFinal);
                        if ($roleFinal == 'USER' || $roleFinal == 'PANEL_USER') {
                            $roleFinal = 'CUSTOMER';
                        }
                    @endphp
                    {{ $roleFinal }}
                </td>
                <td class="text-center">{{ $user->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px; float: right; width: 200px; text-align: center;">
        Dicetak Oleh,<br><br><br><br>
        <strong>( {{ Auth::user()->name }} )</strong>
    </div>
</body>
</html>
