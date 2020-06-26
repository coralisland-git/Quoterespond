@component('mail::message')

User {{ $user['firstname'].' '.$user['lastname'] }} has requested discount for his plan.<br /><br />

@endcomponent