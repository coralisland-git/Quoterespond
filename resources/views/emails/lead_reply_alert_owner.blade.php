@component('mail::message')

Lead {{ ! empty($client->firstname) ? $client->firstname.' '.$client->lastname.' ('.$client->phone.')' : $client->phone }} has send reply.
<br />

@endcomponent