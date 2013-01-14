<h3>{{ Lang::get('confide::confide.signup.title'); }}</h3>
<p>{{ Lang::get('confide::confide.signup.desc'); }}</p>

<form method="POST" action="{{ URL::action('UserController@store'); }}" accept-charset="UTF-8">
    <label for="username">{{ Lang::get('confide::confide.username'); }}</label>
    <input placeholder="{{ Lang::get('confide::confide.username'); }}" type="text" name="username" id="username" value="{{ Input::old('username') }}">

    <label for="email">{{ Lang::get('confide::confide.e_mail'); }} <small>{{ Lang::get('confide::confide.signup.confirmation_required'); }}</small></label>
    <input placeholder="{{ Lang::get('confide::confide.e_mail'); }}" type="text" name="email" id="email" value="{{ Input::old('email') }}">

    <label for="password">{{ Lang::get('confide::confide.password'); }}</label>
    <input placeholder="{{ Lang::get('confide::confide.password'); }}" type="password" name="password" id="password">

    <label for="password2">{{ Lang::get('confide::confide.password2'); }}</label>
    <input placeholder="{{ Lang::get('confide::confide.password2'); }}" type="password2" name="password2" id="password2">
    
    <input class="btn" type="submit" value="{{ Lang::get('confide::confide.signup.submit'); }}">

    @if ( Session::get('error') )
        <p>{{ Session::get('error'); }}</p>
    @endif

    @if ( Session::get('notice') )
        <p>{{ Session::get('notice'); }}</p>
    @endif
</form>
