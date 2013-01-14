<h3>Login</h3>

<form method="POST" action="{{ URL::action('UserController@reset_password'); }}" accept-charset="UTF-8">
    <label for="email">E-mail</label>
    <input placeholder="Email" type="text" name="email" id="email" value="{{ Input::old('email') }}">
    
    <input class="btn" type="submit" value="Continue">

    <p>{{ Session::get('error'); }}</p>
</form>
