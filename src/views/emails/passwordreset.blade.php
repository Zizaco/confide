<h1>{{ Lang::get('confide::confide.email.password_reset.subject') }}</h1>

<p>{{ Lang::get('confide::confide.email.password_reset.greetings', array( 'name' => (isset($user['username'])) ? $user['username'] : $user['email'])) }},</p>

<p>{{ Lang::get('confide::confide.email.password_reset.body') }}</p>
<a href='{{ URL::to('users/reset_password/'.$token) }}'>
    {{ URL::to('users/reset_password/'.$token)  }}
</a>

<p>{{ Lang::get('confide::confide.email.password_reset.farewell') }}</p>
