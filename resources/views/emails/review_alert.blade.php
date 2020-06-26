@component('mail::message')

Hi {{ $user->firstname }}, you just received a {{ $stars }} star review from {{ $client->firstname . ' ' . $client->lastname }}.
@if ($stars == '5')
We automatically sent them to Google to review you there too! Check your reviews here: <a href="{{ $link }}">{{ $link }}</a>.
@else
We followed up and asked why. Click <a href="{{ $link }}">{{ $link }}</a> to see what they wrote.
@endif

<p>Thanks,</p>

ContractorTexter Team

@endcomponent