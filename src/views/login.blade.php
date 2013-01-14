<h3>{{ Lang::get('confide::confide.login.title'); }}</h3>

@if ( Lang::get('confide::confide.login.desc') != '' )
    <p>{{ Lang::get('confide::confide.login.desc'); }}</p>
@endif

<form method="POST" action="{{ URL::action('UserController@do_login'); }}" accept-charset="UTF-8">
    <label for="email">{{ Lang::get('confide::confide.e_mail'); }}</label>
    <input placeholder="{{ Lang::get('confide::confide.e_mail'); }}" type="text" name="email" id="email" value="{{ Input::old('email') }}">

    <label for="password">
        {{ Lang::get('confide::confide.password'); }}
        <small>
            <a href="{{ URL::action('UserController@forgot_password'); }}">{{ Lang::get('confide::confide.login.forgot_password'); }}</a>
        </small>
    </label>
    <input placeholder="{{ Lang::get('confide::confide.password'); }}" type="password" name="password" id="password">

    <label for="remember">{{ Lang::get('confide::confide.login.remamber'); }}</label>
    <input type="hidden" name="remember" value="0">
    <input type="checkbox" name="remember" id="remember" value="1">
    
    <input class="btn" type="submit" value="{{ Lang::get('confide::confide.login.submit'); }}">

    @if ( Session::get('error') )
        <p>{{ Session::get('error'); }}</p>
    @endif

    @if ( Session::get('notice') )
        <p>{{ Session::get('notice'); }}</p>
    @endif
</form>
