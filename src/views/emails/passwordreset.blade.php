<h1>{{ Lang::get('confide::confide.email.password_reset.subject') }}</h1>

<p>{{ Lang::get('confide::confide.email.password_reset.greetings', array( 'name' => $user->username)) }},</p>

<p>{{ Lang::get('confide::confide.email.password_reset.body') }}</p>
<a href='{{{ (Confide::checkAction( Config::get('auth.model') . 'Controller@reset_password', array($token))) ? : URL::to(Config::get('auth.table') . '/reset/'.$token)  }}}'>
    {{{ (Confide::checkAction( Config::get('auth.model') . 'Controller@reset_password', array($token))) ? : URL::to(Config::get('auth.table') . '/reset/'.$token)  }}}
</a>

<p>{{ Lang::get('confide::confide.email.password_reset.farewell') }}</p>
