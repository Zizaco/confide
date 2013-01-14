<h1>{{ Lang::get('confide::confide.email.account_confirmation.subject'); }}</h1>

<p>{{ Lang::get('confide::confide.email.account_confirmation.greetings', array( 'name' => $user->username)); }},</p>

<p>{{ Lang::get('confide::confide.email.account_confirmation.body'); }}</p>
<a href='{{ URL::to('users/activate/').$user->confirmation_key }}'>
    {{ URL::to('users/activate/').$user->confirmation_key }}
</a>

<p>{{ Lang::get('confide::confide.email.account_confirmation.farewell'); }}</p>
