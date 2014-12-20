<?php echo "<?php\n"; ?>{{ strstr($model, '\\') ? ' namespace '.substr($model, 0, -strlen(strrchr($model, '\\'))).';' : '' }}

<?php $nonNamespacedName = (strstr($model, '\\')) ? substr(strrchr($model, '\\'), 1) : $model ?>

use Doctrine\ORM\EntityRepository;
use Mitch\LaravelDoctrine\EntityManagerFacade;
@if (strstr($model, '\\'))
use App;
use Config;
use Confide;
@endif


/**
 * Class UserRepository
 *
 * This service abstracts some interactions that occurs between Confide and
 * the Database.
 */
class UserRepository extends EntityRepository
{
    /**
     * Create a new ConfideRepository
     *
     * @param \Illuminate\Foundation\Application $app Laravel application object
     */
    public function __construct($app = null)
    {
        $app = $app ?: app();

        $em = $app['Doctrine\ORM\EntityManager'];

        parent::__construct($em, new \Doctrine\ORM\Mapping\ClassMetadata('User'));
    }

    /**
     * Signup a new account with the given parameters
     *
     * @param array $input Array containing 'username', 'email' and 'password'.
     *
     * @return {{ $nonNamespacedName }} {{ $nonNamespacedName }} object that may or may not be saved successfully. Check the id to make sure.
     */
    public function signup($input)
    {
        $user = new {{ $nonNamespacedName }};

        $user->setUsername(array_get($input, 'username'));
        $user->setEmail(array_get($input, 'email'));
        $user->setPassword(array_get($input, 'password'));

        // The password confirmation will be removed from model
        // before saving. This field will be used in Ardent's
        // auto validation.
        $user->setPasswordConfirmation(array_get($input, 'password_confirmation'));

        // Generate a random confirmation code
        $user->setConfirmationCode(md5(uniqid(mt_rand(), true)));

        // Save if valid. Password field will be hashed before save
        $this->save($user);

        return $user;
    }

    /**
     * Attempts to login with the given credentials.
     *
     * @param array $input Array containing the credentials (email/username and password)
     *
     * @return boolean Success?
     */
    public function login($input)
    {
        if (! isset($input['password'])) {
            $input['password'] = null;
        }

        return Confide::logAttempt($input, Config::get('confide::signup_confirm'));
    }

    /**
     * Checks if the credentials has been throttled by too
     * much failed login attempts
     *
     * @param array $credentials Array containing the credentials (email/username and password)
     *
     * @return boolean Is throttled
     */
    public function isThrottled($input)
    {
        return Confide::isThrottled($input);
    }

    /**
     * Checks if the given credentials correponds to a user that exists but
     * is not confirmed
     *
     * @param array $credentials Array containing the credentials (email/username and password)
     *
     * @return boolean Exists and is not confirmed?
     */
    public function existsButNotConfirmed($input)
    {
        $user = Confide::getUserByEmailOrUsername($input);

        if ($user) {
            $correctPassword = Hash::check(
                isset($input['password']) ? $input['password'] : false,
                $user->getPassword()
            );

            return (! $user->getConfirmed() && $correctPassword);
        }
    }

    /**
     * Resets a password of a user. The $input['token'] will tell which user.
     *
     * @param array $input Array containing 'token', 'password' and 'password_confirmation' keys.
     *
     * @return boolean Success
     */
    public function resetPassword($input)
    {
        $result = false;
        $user   = Confide::userByResetPasswordToken($input['token']);

        if ($user) {
            $user->setPassword($input['password']);
            $user->setPasswordConfirmation($input['password_confirmation']);
            $result = $this->save($user);
        }

        // If result is positive, destroy token
        if ($result) {
            Confide::destroyForgotPasswordToken($input['token']);
        }

        return $result;
    }

    /**
     * Simply saves the given instance
     *
     * @param {{ $nonNamespacedName }} $instance
     *
     * @return boolean Success
     */
    public function save({{ $nonNamespacedName }} $instance)
    {
        if (!$instance->isValid()) {
            return false;
        }

        $em = $this->getEntityManager();
        $em->persist($instance);
        $em->flush();
    }
}
