<form method="POST" action="{{{ (URL::action('UserController@do_forgot_password')) ?: URL::to('/user/forgot') }}}" accept-charset="UTF-8">
    <input type="hidden" name="csrf_token" value="{{{ Session::getToken() }}}">

    <label for="email">{{{ Lang::get('confide::confide.e_mail') }}}</label>
    <div class="input-append">
        <input placeholder="{{{ Lang::get('confide::confide.e_mail') }}}" type="text" name="email" id="email" value="{{{ Input::old('email') }}}">
        
        <input class="btn" type="submit" value="{{{ Lang::get('confide::confide.forgot.submit') }}}">
    </div>

    @if ( Session::get('error') )
        <div class="alert alert-error">{{{ Session::get('error') }}}</div>
    @endif

    @if ( Session::get('notice') )
        <div class="alert">{{{ Session::get('notice') }}}</div>
    @endif
</form>
