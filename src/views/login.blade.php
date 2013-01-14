<h3>Login</h3>

<form method="POST" action="{{ URL::action('UserController@do_login'); }}" accept-charset="UTF-8">
    <label for="email">E-mail</label>
    <input placeholder="Email" type="text" name="email" id="email" value="{{ Input::old('email') }}">

    <label for="password">
        Password 
        <small>
            <a href="{{ URL::action('UserController@forgot_password'); }}">(forgot password)</a>
        </small>
    </label>
    <input placeholder="Password" type="password" name="password" id="password">

    <label for="remember">Remember me</label>
    <input type="hidden" name="remember" value="0">
    <input type="checkbox" name="remember" id="remember" value="1">
    
    <input class="btn" type="submit" value="Login">

    <p>{{ Session::get('error'); }}</p>
    <p>{{ Session::get('notice'); }}</p>
</form>
