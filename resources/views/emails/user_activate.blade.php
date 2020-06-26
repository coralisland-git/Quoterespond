@component('mail::message')
<p>
	User {{ $user['firstname'] . ' ' . $user['lastname'] }} has entered his CC details and ready to go live.
</p>
@endcomponent
