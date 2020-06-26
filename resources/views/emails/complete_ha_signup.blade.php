@component('mail::message')

Hi {{ $user->firstname }} - Welcome aboard!<br />

<b>1.</b> We are connecting you now. This can take several days, but don't worry - your free trial won't start until you are live.<br />
<b>2.</b> To go to your dashboard anytime, click <a href="{{ config('app.url') }}">app.contractortexter.com</a>.<br />
<b>3.</b> Want 3 months free? Send us the name and contact info for a friend who would benefit from us - if they sign up, you save big!<br />

<p>Thanks,</p>

ContractorTexter Team
@endcomponent