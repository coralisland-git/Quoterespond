@component('mail::message')

{{ $user->firstname.' '.$user->lastname }} - your lead {{ ! empty($client->firstname) ? $client->firstname.' '.$client->lastname.' ('.$client->phone.')' : $client->phone }},
clicked the link in your text and is on your site - click <a href="{{ $link }}">{{ $link }}</a> to text more if you like!
<br />

<b>Want a free month? Refer a friend! Just email us their contact info - thanks!</b>

@endcomponent