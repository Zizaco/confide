{{ '<?php' }}


/*
|--------------------------------------------------------------------------
| Confide Controller Template
|--------------------------------------------------------------------------
|
| This is the default Confide controller template for controlling user
| authentication. Feel free to change to your needs.
|
*/

class {{ $name }} extends BaseController {

    /**
     * Displays the form for account creation
     *
     */
    public function create()
    {
        return Confide::MakeSignupForm();
    }

    /**
     * Stores new account
     *
     */
    public function store()
    {
        ${{ lcfirst(Config::get('auth.model')) }} = new {{ Config::get('auth.model') }};
        ${{ lcfirst(Config::get('auth.model')) }}->username = Input::get( 'username' );
        ${{ lcfirst(Config::get('auth.model')) }}->email = Input::get( 'email' );
        ${{ lcfirst(Config::get('auth.model')) }}->password = Input::get( 'password' );

        ${{ lcfirst(Config::get('auth.model')) }}->save();

        if ( ${{ lcfirst(Config::get('auth.model')) }}->id )
        {
            return Redirect::action('{{ $name }}@login');
        }
        else
        {
            return Redirect::action('{{ $name }}@create')
                ->withInput(Input::except('password'));
        }
    }

    /**
     * Displays the login form
     *
     */
    public function login()
    {
        return Confide::MakeLoginForm();
    }

    /**
     * Attempt to do login
     *
     */
    public function do_login()
    {
        $input = array(
            'email' => Input::get( 'email' ),
            'password' => Input::get( 'password' ),
        );

        if ( Auth::attempt( $input, Input::get( 'remember' ) ) ) 
        {
            return Redirect::to('/');
        }
        else
        {
            $err_msg = "Incorrect e-mail or password.";
            return Redirect::action('{{ $name }}@login')
                ->withInput(Input::except('password'))
                ->with( 'error', $err_msg );
        }
    }

    /**
     * Attempt to confirm account with code
     *
     * @param  string  $code
     */
    public function confirm( $code )
    {
        if ( Confide::Confirm( $code ) )
        {
            $notice_msg = "Your account has been confirmed! You may now login.";
            return Redirect::action('{{ $name }}@login')
                ->with( 'notice', $notice_msg );
        }
        else
        {
            $error_msg = "Wrong confirmation code.";
            return Redirect::action('{{ $name }}@login')
                ->with( 'error', $error_msg );
        }
    }

    /**
     * Displays the forgot password form
     *
     */
    public function forgot_password()
    {
        return Confide::MakeForgetPasswordForm();
    }

    /**
     * Attempt to reset password with given email
     *
     */
    public function reset_password()
    {
        if( Confide::ResetPassword( Input::get( 'email' ) ) )
        {
            $notice_msg = "A new password has been sent to your email.";
            return Redirect::action('{{ $name }}@login')
                ->with( 'notice', $notice_msg );
        }
        else
        {
            $error_msg = "User not found.";
            return Redirect::action('{{ $name }}@forgot_password')
                ->withInput()
                ->with( 'error', $error_msg );
        }
    }

}
