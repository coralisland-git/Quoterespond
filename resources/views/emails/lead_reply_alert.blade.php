@component('mail::message')

{{ $user->firstname.' '.$user->lastname }} - your lead {{ ! empty($client->firstname) ? $client->firstname.' '.$client->lastname.' ('.$client->phone.')' : $client->phone }}
texted you back - click <a href="{{ $link }}">{{ $link }}</a> to see and respond!
<br />

<b>Want a free month? Refer a friend! Just email us their contact info - thanks!</b>

@endcomponent