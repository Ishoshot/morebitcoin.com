<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'MoreBitcoin')
<img src="https://res.cloudinary.com/oluwatobi/image/upload/v1641395119/logo_rg83du.png" class="logo" alt="More Bitcoin">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
