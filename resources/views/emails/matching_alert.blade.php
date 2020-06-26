@component('mail::message')
You have new match from {{ $matching['source'] }}<br /><br />
<b>Name: </b>{{ $matching['firstname'] }}<br />
<b>Cell #: </b>{{ $matching['phone'] }}<br />
<b>Unknown user name string: </b>{{ $name_string }}<br />

<a href="{{ $url }}/matchings/list/">{{ $url }}/matchings/list/</a>

@endcomponent