@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
            <img src="{{ config('app.url') }}/images/logo.png" class="logo" alt="Pt Sinar Pekasa Abadi" style="height: 50px;">
            @else
            {{ $slot }}
            @endif
        </a>
    </td>
</tr>