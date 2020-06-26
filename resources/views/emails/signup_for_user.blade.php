@component('mail::message')

Hi {{ $user->firstname }},<br />
<br />
Thanks for signing up - but before we can get you started, we just need your HomeAdvisor account number and a credit card
(we don’t charge it until after the trial of course).<br />
<p>To finish your signup: <a href="https://app.contractortexter.com">app.contractortexter.com</a></p>
<p>To schedule a call with us: <a href="https://calendly.com/contractortexter">calendly.com/contractortexter</a></p>
Appreciate it!<br />
ContractorTexter Team
@endcomponent