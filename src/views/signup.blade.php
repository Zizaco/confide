<h3>Signup</h3>
<p>Signup for a new account</p>

<form method="POST" action="{{ URL::action('UserController@store'); }}" accept-charset="UTF-8">
    <label for="username">Username</label>
    <input placeholder="Username" type="text" name="username" id="username" value="{{ Input::old('username') }}">

    <label for="email">E-mail <small>Confirmation required</small></label>
    <input placeholder="Email" type="text" name="email" id="email" value="{{ Input::old('email') }}">

    <label for="password">Password</label>
    <input placeholder="Password" type="password" name="password" id="password">
    
    <input class="btn" type="submit" value="Create new account">

    <p>{{ Session::get('error'); }}</p>
</form>
