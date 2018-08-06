@component('mail::message')
# One last step

We just need you to confirm your email address so that we know you are a human.

@component('mail::button', ['url' => url('/register/confirm?token=' . $user->confirmation_token)])
Confirm
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
