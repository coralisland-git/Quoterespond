@component('mail::message')

User {{ $user['firstname'].' '.$user['lastname'] }} has canceled his plan.<br /><br />
Cancellation reason: {{ $user['cancellation_reason'] }}

@endcomponent