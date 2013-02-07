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
    public function {{ (! $restful) ? 'create' : 'getCreate' }}()
    {
        return Confide::makeSignupForm();
    }

    /**
     * Stores new account
     *
     */
    public function {{ (! $restful) ? 'store' : 'postIndex' }}()
    {
        ${{ lcfirst(Config::get('auth.model')) }} = new {{ Config::get('auth.model') }};

        ${{ lcfirst(Config::get('auth.model')) }}->username = Input::get( 'username' );
        ${{ lcfirst(Config::get('auth.model')) }}->email = Input::get( 'email' );
        ${{ lcfirst(Config::get('auth.model')) }}->password = Input::get( 'password' );

        // The password confirmation will be removed from model
        // before saving. This field will be used in Ardent's
        // auto validation.
        ${{ lcfirst(Config::get('auth.model')) }}->password_confirmation = Input::get( 'password_confirmation' );

        // Save if valid. Password field will be hashed before save
        ${{ lcfirst(Config::get('auth.model')) }}->save();

        if ( ${{ lcfirst(Config::get('auth.model')) }}->id )
        {
            // Redirect with success message, You may replace "Lang::get(..." for your custom message.
            @if (! $restful)
            return Redirect::action('{{ $name }}@login')
            @else
            return Redirect::to('user/login')
            @endif
                ->with( 'notice', Lang::get('confide::confide.alerts.account_created') );
        }
        else
        {
            // Get validation errors (see Ardent package)
            $error = ${{ lcfirst(Config::get('auth.model')) }}->getErrors()->all();

            @if (! $restful)
            return Redirect::action('{{ $name }}@create')
            @else
            return Redirect::to('user/create')
            @endif
                ->withInput(Input::except('password'))
                ->with( 'error', $error );
        }
    }

    /**
     * Displays the login form
     *
     */
    public function {{ (! $restful) ? 'login' : 'getLogin' }}()
    {
        return Confide::makeLoginForm();
    }

    /**
     * Attempt to do login
     *
     */
    public function {{ (! $restful) ? 'do_login' : 'postLogin' }}()
    {
        $input = array(
            'email'    => Input::get( 'email' ), // May be the username too
            'password' => Input::get( 'password' ),
            'remamber' => Input::get( 'remember' ),
        );

        // If you wish to only allow login from confirmed users, call logAttempt
        // with the second parameter as true.
        // logAttempt will check if the 'email' perhaps is the username.
        if ( Confide::logAttempt( $input ) ) 
        {
            return Redirect::to('/');
        }
        else
        {
            if( Confide::isThrottled( $input ) )
            {
                $err_msg = Lang::get('confide::confide.alerts.too_many_attempts');
            }
            else
            {
                $err_msg = Lang::get('confide::confide.alerts.wrong_credentials');
            }

            @if (! $restful)
            return Redirect::action('{{ $name }}@login')
            @else
            return Redirect::to('user/login')
            @endif
                ->withInput(Input::except('password'))
                ->with( 'error', $err_msg );
        }
    }

    /**
     * Attempt to confirm account with code
     *
     * @param  string  $code
     */
    public function {{ (! $restful) ? 'confirm' : 'getConfirm' }}( $code )
    {
        if ( Confide::confirm( $code ) )
        {
            $notice_msg = Lang::get('confide::confide.alerts.confirmation');
            @if (! $restful)
            return Redirect::action('{{ $name }}@login')
            @else
            return Redirect::to('user/login')
            @endif
                ->with( 'notice', $notice_msg );
        }
        else
        {
            $error_msg = Lang::get('confide::confide.alerts.wrong_confirmation');
            @if (! $restful)
            return Redirect::action('{{ $name }}@login')
            @else
            return Redirect::to('user/login')
            @endif
                ->with( 'error', $error_msg );
        }
    }

    /**
     * Displays the forgot password form
     *
     */
    public function {{ (! $restful) ? 'forgot_password' : 'getForgot' }}()
    {
        return Confide::makeForgetPasswordForm();
    }

    /**
     * Attempt to reset password with given email
     *
     */
    public function {{ (! $restful) ? 'reset_password' : 'postForgot' }}()
    {
        if( Confide::resetPassword( Input::get( 'email' ) ) )
        {
            $notice_msg = Lang::get('confide::confide.alerts.password_reset');
            @if (! $restful)
            return Redirect::action('{{ $name }}@login')
            @else
            return Redirect::to('user/login')
            @endif
                ->with( 'notice', $notice_msg );
        }
        else
        {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_reset');
            @if (! $restful)
            return Redirect::action('{{ $name }}@forgot_password')
            @else
            return Redirect::to('user/forgot')
            @endif
                ->withInput()
                ->with( 'error', $error_msg );
        }
    }

    /**
     * Log the user out of the application.
     *
     */
    public function {{ (! $restful) ? 'logout' : 'getLogout' }}()
    {
        Confide::logout();
        
        return Redirect::to('/');
    }

}
