@component('mail::message')

Hi {{ $firstname }},<br />

Just a quick recap of your past week. <br />

<ul>
	<li><p>{{ $clicked_count }} clicked the link going to your site</p></li>
	<li><p>{{ $replied_count }} texted you back</p></li>
	<li><p>Leads who engaged: {{ $leads_names }}</p></li>
</ul>

<p>You can log in anytime here: <a href="{{ config('app.url') }}">app.contractortexter.com</a>. Please let us know if you have any questions - thanks!</p>

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent