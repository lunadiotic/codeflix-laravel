@component('mail::message')
# Hello!

Your membership has expired.

Expired Date: {{ $expiredDate }}

@component('mail::button', ['url' => $renewUrl])
Renew Membership
@endcomponent

Thank you for using our application!
@endcomponent