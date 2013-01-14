<h1>Account Confirmation</h1>

<p>Hi {{ $user->username }},</p>

<p>Please access the link bellow to confirm your account:</p>
<a href='{{ URL::to('users/activate/').$user->confirmation_key }}'>
    {{ URL::to('users/activate/').$user->confirmation_key }}
</a>

<p>Regards</p>
