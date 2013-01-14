<h3>{{ Lang::get('confide::confide.forgot.title'); }}</h3>

<form method="POST" action="{{ URL::action('UserController@reset_password'); }}" accept-charset="UTF-8">
    <label for="email">{{ Lang::get('confide::confide.e_mail'); }}</label>
    <input placeholder="{{ Lang::get('confide::confide.e_mail'); }}" type="text" name="email" id="email" value="{{ Input::old('email') }}">
    
    <input class="btn" type="submit" value="{{ Lang::get('confide::confide.forgot.submit'); }}">

    <p>{{ Session::get('error'); }}</p>
</form>
