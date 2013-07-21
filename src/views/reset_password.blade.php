<form method="POST" action="{{{ (Confide::checkAction('UserController@do_reset_password'))    ?: URL::to('/user/reset') }}}" accept-charset="UTF-8">
    <input type="hidden" name="token" value="{{{ $token }}}">
    <input type="hidden" name="_token" value="{{{ Session::getToken() }}}">

    <label for="password">{{{ Lang::get('confide::confide.password') }}}</label>
    <input placeholder="{{{ Lang::get('confide::confide.password') }}}" type="password" name="password" id="password">

    <label for="confirmation_code">{{{ Lang::get('confide::confide.confirmation_code') }}}</label>
    <input placeholder="{{{ Lang::get('confide::confide.confirmation_code') }}}" type="password" name="confirmation_code" id="confirmation_code">

    @if ( Session::get('error') )
        <div class="alert alert-error">{{{ Session::get('error') }}}</div>
    @endif

    @if ( Session::get('notice') )
        <div class="alert">{{{ Session::get('notice') }}}</div>
    @endif

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">{{{ Lang::get('confide::confide.forgot.submit') }}}</button>
    </div>
</form>
