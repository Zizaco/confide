<?php echo "<?php\n"; ?>{{ $namespace ? ' namespace '.$namespace.';' : '' }}

<?php $repositoryClass = strstr($model, '\\') ? substr($model, 0, -strlen(strrchr($model, '\\'))).'\UserRepository' : 'UserRepository' ?>
@if ($namespace)

use App;
use View;
use Input;
use Config;
use Redirect;
use Lang;
use Mail;
use Confide;
use Controller;
@endif

/**
 * UsersController Class
 *
 * Implements actions regarding user management
 */
class {{ $class }} extends Controller
{

    /**
     * Displays the form for account creation
     *
     * @return Illuminate\Http\Response
     */
    public function {{ (! $restful) ? 'create' : 'getCreate' }}()
    {
        return View::make(Config::get('confide::signup_form'));
    }

    /**
     * Stores new account
     *
     * @return Illuminate\Http\Response
     */
    public function {{ (! $restful) ? 'store' : 'postIndex' }}()
    {
        $repo = App::make('{{ $repositoryClass }}');
        $user = $repo->signup(Input::all());

        if ($user->id) {
            if (Config::get('confide::signup_email')) {
                Mail::queueOn(
                    Config::get('confide::email_queue'),
                    Config::get('confide::email_account_confirmation'),
                    compact('user'),
                    function ($message) use ($user) {
                        $message
                            ->to($user->email, $user->username)
                            ->subject(Lang::get('confide::confide.email.account_confirmation.subject'));
                    }
                );
            }

            return Redirect::action('{{ $namespace ? $namespace.'\\' : '' }}{{ $class }}{{ (! $restful) ? '@login' : '@getLogin' }}')
                ->with('notice', Lang::get('confide::confide.alerts.account_created'));
        } else {
            $error = $user->errors()->all(':message');

            return Redirect::action('{{ $namespace ? $namespace.'\\' : '' }}{{ $class }}{{ (! $restful) ? '@create' : '@getCreate' }}')
                ->withInput(Input::except('password'))
                ->with('error', $error);
        }
    }

    /**
     * Displays the login form
     *
     * @return Illuminate\Http\Response
     */
    public function {{ (! $restful) ? 'login' : 'getLogin' }}()
    {
        if (Confide::user()) {
            return Redirect::to('/');
        } else {
            return View::make(Config::get('confide::login_form'));
        }
    }

    /**
     * Attempt to do login
     *
     * @return Illuminate\Http\Response
     */
    public function {{ (! $restful) ? 'doLogin' : 'postLogin' }}()
    {
        $repo = App::make('{{ $repositoryClass }}');
        $input = Input::all();

        if ($repo->login($input)) {
            return Redirect::intended('/');
        } else {
            if ($repo->isThrottled($input)) {
                $err_msg = Lang::get('confide::confide.alerts.too_many_attempts');
            } elseif ($repo->existsButNotConfirmed($input)) {
                $err_msg = Lang::get('confide::confide.alerts.not_confirmed');
            } else {
                $err_msg = Lang::get('confide::confide.alerts.wrong_credentials');
            }

            return Redirect::action('{{ $namespace ? $namespace.'\\' : '' }}{{ $class }}{{ (! $restful) ? '@login' : '@getLogin' }}')
                ->withInput(Input::except('password'))
                ->with('error', $err_msg);
        }
    }

    /**
     * Attempt to confirm account with code
     *
     * @param string $code
     *
     * @return Illuminate\Http\Response
     */
    public function {{ (! $restful) ? 'confirm' : 'getConfirm' }}($code)
    {
        if (Confide::confirm($code)) {
            $notice_msg = Lang::get('confide::confide.alerts.confirmation');
            return Redirect::action('{{ $namespace ? $namespace.'\\' : '' }}{{ $class }}{{ (! $restful) ? '@login' : '@getLogin' }}')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_confirmation');
            return Redirect::action('{{ $namespace ? $namespace.'\\' : '' }}{{ $class }}{{ (! $restful) ? '@login' : '@getLogin' }}')
                ->with('error', $error_msg);
        }
    }

    /**
     * Displays the forgot password form
     *
     * @return Illuminate\Http\Response
     */
    public function {{ (! $restful) ? 'forgotPassword' : 'getForgot' }}()
    {
        return View::make(Config::get('confide::forgot_password_form'));
    }

    /**
     * Attempt to send change password link to the given email
     *
     * @return Illuminate\Http\Response
     */
    public function {{ (! $restful) ? 'doForgotPassword' : 'postForgot' }}()
    {
        if (Confide::forgotPassword(Input::get('email'))) {
            $notice_msg = Lang::get('confide::confide.alerts.password_forgot');
            return Redirect::action('{{ $namespace ? $namespace.'\\' : '' }}{{ $class }}{{ (! $restful) ? '@login' : '@getLogin' }}')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_forgot');
            return Redirect::action('{{ $namespace ? $namespace.'\\' : '' }}{{ $class }}{{ (! $restful) ? '@doForgotPassword' : '@postForgot' }}')
                ->withInput()
                ->with('error', $error_msg);
        }
    }

    /**
     * Shows the change password form with the given token
     *
     * @param string $token
     *
     * @return Illuminate\Http\Response
     */
    public function {{ (! $restful) ? 'resetPassword' : 'getReset' }}($token)
    {
        return View::make(Config::get('confide::reset_password_form'))
                ->with('token', $token);
    }

    /**
     * Attempt change password of the user
     *
     * @return Illuminate\Http\Response
     */
    public function {{ (! $restful) ? 'doResetPassword' : 'postReset' }}()
    {
        $repo = App::make('{{ $repositoryClass }}');
        $input = array(
            'token'                 =>Input::get('token'),
            'password'              =>Input::get('password'),
            'password_confirmation' =>Input::get('password_confirmation'),
        );

        // By passing an array with the token, password and confirmation
        if ($repo->resetPassword($input)) {
            $notice_msg = Lang::get('confide::confide.alerts.password_reset');
            return Redirect::action('{{ $namespace ? $namespace.'\\' : '' }}{{ $class }}{{ (! $restful) ? '@login' : '@getLogin' }}')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_reset');
            return Redirect::action('{{ $namespace ? $namespace.'\\' : '' }}{{ $class }}{{ (! $restful) ? '@resetPassword' : '@getReset' }}', array('token'=>$input['token']))
                ->withInput()
                ->with('error', $error_msg);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @return Illuminate\Http\Response
     */
    public function {{ (! $restful) ? 'logout' : 'getLogout' }}()
    {
        Confide::logout();

        return Redirect::to('/');
    }
}
