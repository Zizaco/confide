# Confide _(A Laravel4 Package)_

![Confide Poster](https://dl.dropbox.com/u/12506137/libs_bundles/confide.png)

[![Build Status](https://api.travis-ci.org/Zizaco/confide.png)](https://travis-ci.org/Zizaco/confide)
[![Coverage Status](https://coveralls.io/repos/Zizaco/confide/badge.png?branch=master)](https://coveralls.io/r/Zizaco/confide?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Zizaco/confide/badges/quality-score.png)](https://scrutinizer-ci.com/g/Zizaco/confide/)
[![ProjectStatus](http://stillmaintained.com/Zizaco/confide.png)](http://stillmaintained.com/Zizaco/confide)
[![Latest Stable Version](https://poser.pugx.org/zizaco/confide/v/stable.png)](https://packagist.org/packages/zizaco/confide)
[![Total Downloads](https://poser.pugx.org/zizaco/confide/downloads.png)](https://packagist.org/packages/zizaco/confide)
[![License](https://poser.pugx.org/zizaco/confide/license.png)](http://opensource.org/licenses/MIT)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/ec420846-0af4-4df4-b424-be90d9c3f98e/small.png)](https://insight.sensiolabs.com/projects/ec420846-0af4-4df4-b424-be90d9c3f98e)

Confide is an authentication solution for **Laravel** made to cut repetitive work involving the management of users. A [DRY](http://en.wikipedia.org/wiki/Don't_repeat_yourself) approach on features like account creation, login, logout, confirmation by e-mail, password reset, etc.

Confide aims to be simple to use, quick to configure and flexible.

> Note: If you are using MongoDB check [Confide Mongo](https://github.com/Zizaco/confide-mongo).

## Features

**Current:**
- Account confirmation (through confirmation link).
- Password reset (sending email with a change password link).
- Easily render forms for login, signup and password reset.
- Generate routes for login, signup, password reset, confirmation, etc.
- Generate a customizable controller that handles the basic user account actions.
- Contains a set of methods to help with basic user features.
- Integrated with the Laravel _Auth_ and _Reminders_ component/configs.
- User validation.
- Login throttling.
- Redirecting to previous route after authentication.
- Checks for unique email and username in signup

If you are looking for user roles and permissions see [Entrust](https://github.com/Zizaco/entrust)

For MongoDB support see [Confide Mongo](https://github.com/Zizaco/confide-mongo)

**Warning:** _By default a confirmation email is sent and users are required to confirm the email address.
It is easy to change this in the confide config file.
Change `signup_email` and `signup_confirm` to false if you do not want to send them an email and they do not need
to be confirmed to be able to login to the website._

## Quick start

### Required setup

In the `require` key of `composer.json` file add the following

```json
"zizaco/confide": "~4.3@dev"
```

Run the Composer update comand

```bash
$ composer update
```

In your `config/app.php` add `'Zizaco\Confide\ServiceProvider'` to the end of the `providers` array

```php
'providers' => array(

    'Illuminate\Foundation\Providers\ArtisanServiceProvider',
    'Illuminate\Auth\AuthServiceProvider',
    ...
    'Zizaco\Confide\ServiceProvider',

),
```

At the end of `config/app.php` add `'Confide' => 'Zizaco\Confide\Facade'` to the `aliases` array

```php
'aliases' => array(

    'App'        => 'Illuminate\Support\Facades\App',
    'Artisan'    => 'Illuminate\Support\Facades\Artisan',
    ...
    'Confide'    => 'Zizaco\Confide\Facade',

),
```

### Configuration

Set the properly values to the `config/auth.php`. This values will be used by confide to generate the database migration and to generate controllers and routes.

Set the `address` and `name` from the `from` array in `config/mail.php`. Those will be used to send account confirmation and password reset emails to the users.

<a name="user-model"></a>
### User model

Now generate the Confide migration and the reminder password table migration:

```bash
$ php artisan confide:migration
```

It will generate the `<timestamp>_confide_setup_users_table.php` migration. You may now run it with the artisan migrate command:

```bash
$ php artisan migrate
```

It will setup a table containing `email`, `password`, `remember_token`, `confirmation_code` and `confirmed` columns, which are the default fields needed for Confide use. Feel free to add more columns to the table later.

Change your User model in `app/models/User.php` to:

```php
<?php

use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\ConfideUserInterface;

class User extends Eloquent implements ConfideUserInterface
{
    use ConfideUser;
}
```

`ConfideUser` trait will take care of some behaviors of the user model.

### Dump the default accessors

Lastly, you can dump a default controller, repository and the default routes for Confide.

```bash
$ php artisan confide:controller
$ php artisan confide:routes
```

Don't forget to dump composer autoload

```bash
$ composer dump-autoload
```

**And you are ready to go.** Access `http://yourapp/users/create` to create your first user. Check the `app/routes.php` to see the available routes. **You may need to confirm a newly created user** _(by "reaching" its `confirm()` method)_, otherwise you can disable the confirmation as a requirement to login in in the config file _(see bellow)_.


## Usage in detail

**Basic setup:**

1. Database connection in `config/database.php` running properly.
2. Correct model and table names in `config/auth.php`. They will be used by Confide all the time _(specially when generating migrations and controllers)_.
3. `from` configuration in `config/mail.php`.

**Configuration:**

1. `'Zizaco\Confide\ServiceProvider'` and `'Confide' => 'Zizaco\Confide\Facade'` entry in `config/app.php` `'providers'` and `'aliases'` respectively.
2. User model (with the same name as in `config/auth.php`) should implement `Zizaco\Confide\ConfideUserInterface` interface. This will cause to methods like `forgotPassword()` and `confirm()` to be available.

**Optional steps:**

1. Optionally you can use the trait `Zizaco\Confide\ConfideUser` in your user model. This will save a lot of time and will use "confide's default" implementation for the user. If you wish more customization you can write your own code.
2. Use `Confide` facade to dump login and signup forms easly with `makeLoginForm()` and `makeSignupForm()`. You can render the forms within your views by doing `{{ Confide::makeLoginForm()->render() }}`.
3. Generate a **controller** and a **repository** with the template contained in Confide throught the artisan command `$ php artisan confide:controller`. If a controller with the same name exists it will **NOT** be overwritten.
4. Generate routes matching the controller template throught the artisan command `$ php artisan confide:routes`. Don't worry, your `routes.php` will **NOT** be overwritten.

### Advanced

#### The `UserRepository` class

You may have noticed that when generating the controller a `UserRepository` class has also been created. This class contains some code that doesn't belong to the "controller" purpose and will make your users controller a cleaner and more testable class. If you still have no idea why that class exists I recommend you to google _"Creating flexible Controllers in Laravel 4 using Repositories"_. _(wink)_

#### Using custom class, table or model name

You can change the model name that will be considered the user in the `config/auth.php` file.
Confide uses the values present in that configuration file.

To change the controller name when dumping the default controller template you can use the --name option.

```bash
$ php artisan confide:controller --name=Employee
```

Will result in `EmployeeController`

Then, when dumping the routes, you should use the --controller option to match the existing controller.

```bash
$ php artisan confide:routes --controller=Employee
```

You can also generate controllers with namespace

```bash
$ php artisan confide:controller --name=MyProject\\Auth\\User
```

**Warning:** In bash, you will need to use double '\\\\' backslashes. This will result in `MyProject\Auth\UserController`. Also the generated file will be inside a directory equivalent to the namespace. _(wink)_

#### Using custom form or emails

First, publish the config files:

```bash
$ php artisan config:publish zizaco/confide
```

Then edit the view names in `app/config/packages/zizaco/confide/config.php`.

#### Seeding

To seed your users table you should fill also the `password_confirmation` and `confirmation_code` fields. For example:

```php
class UsersTableSeeder extends Seeder {

    public function run()
    {
        $user = new User;
        $user->email = 'johndoe@site.dev';
        $user->password = 'foo_bar_1234';
        $user->password_confirmation = 'foo_bar_1234';
        $user->confirmation_code = md5(uniqid(mt_rand(), true));
        $user->confirmed = 1;

        if(! $user->save()) {
            Log::info('Unable to create user '.$user->email, (array)$user->errors());
        } else {
            Log::info('Created user '.$user->email);
        }
    }
}
```

#### Custom user validation

You can implement your own validator by creating a class that implements the `UserValidatorInterface` and registering that class as *"confide.user_validator"*.

For example, create your custom validator class:

```php
// app/models/MyOwnValidator.php
class MyOwnValidator implements UserValidatorInterface
{

    public function validate(ConfideUserInterface $user)
    {
        unset($user->password_confirmation);
        return true; // If the user valid
    }
}
```

Then register it in IoC container as *"confide.user_validator"*

```php
// app/start/global.php
//...
App::bind('confide.user_validator', 'MyOwnValidator');
```

Also, don't forget that your validator should unset the 'password_confirmation' attribute of the user before saving it.

#### Passing additional information to the "make" methods

If you want to pass additional parameters to the forms being rendered, you can use an alternate syntax to achieve this.

Instead of using the make method:

```php
Confide::makeResetPasswordForm($token):
```

You would use:

```php
View::make(Config::get('confide::reset_password_form'))
    ->with('token', $token);
```

It produces the same output, but you would be able to add more inputs using 'with' just like any other view.

#### RESTful controller

If you want to generate a [RESTful controller](https://github.com/laravel/docs/blob/master/controllers.md#restful-controllers) you can use the aditional `--restful` or `-r` option.

```bash
$ php artisan confide:controller --restful
```

Will result in a [RESTful controller](https://github.com/laravel/docs/blob/master/controllers.md#restful-controllers)

Then, when dumping the routes, you should use the --restful option to match the existing controller.

```bash
$ php artisan confide:routes --restful
```

#### User roles and permissions

In order not to bloat Confide with not related features, the role and permission was developed as another package: [Entrust](https://github.com/Zizaco/entrust). Enstrust couples very well with Confide.

See [Entrust](https://github.com/Zizaco/entrust)

#### Redirecting to previous route after login

When defining your filter you should use the Redirect::guest('users/login') within your auth filter. For example:

```php
// filters.php

Route::filter('auth', function () {
    // If the user is not logged in
    if (Auth::guest()) {
        return Redirect::guest('users/login');
    }
});

// Only authenticated users will be able to access routes that begins with
// 'admin'. Ex: 'admin/posts', 'admin/categories'.
Route::when('admin*', 'auth');
```

or, if you are using [Entrust](https://github.com/Zizaco/entrust) ;)

```php
// filters.php

Entrust::routeNeedsRole('admin*', 'Admin', function () {
    return Redirect::guest('users/login');
});
```

Finally, it'll auto redirect if your controller's users/login function uses Redirect:intended('a/default/url/here') after a successful login.
The [generated controller](https://github.com/Zizaco/confide/blob/master/src/views/generators/controller.blade.php) does exactly this.

## Troubleshooting

__[2014-07-18 01:13:15] production.ERROR: exception 'Illuminate\Database\QueryException' with message 'SQLSTATE[42S22]: Column not found: 1054 Unknown column 'password_confirmation' in 'field list' (SQL: insert into \`users\` ...__

The `password_confirmation` attribute should be removed from the object before being sent to the database. Make sure your user model implement the `ConfideUserInterface` and that it use the `ConfideUser` trait [as described above](#user-model). Otherwise if you are using a custom validator, you will have to unset `password_confirmation` before saving the user.

__I need my users to have an *"username"*__

Use the `--username` option when generating the confide migration and the controller.

    $ php artisan confide:migration --username
    ...
    $ php artisan confide:controller --username

If you want to make the username a required field you will have to [extend the `UserValidator`](#custom-user-validation) and overwrite the `$rules` attribute making the _"username"_ `required`.

__I receive a "Your account may not be confirmed" when trying to login__

You need to confirm a newly created user _(by "reaching" its `confirm()` method)_, otherwise you can disable the confirmation as a requirement to login in in the config file _(see bellow)_. You can easly confirm an user manually using Laravel's `artisan tinker` tool.

__I'm not able to generate a controller with namespaces__

In bash, you will need to use double '\\\\' backslashes. Also the generated file will be inside a directory equivalent to the namespace:

    $ php artisan confide:controller --name=MyProject\\Auth\\User

__Users are able to login without confirming account__

If you want only confirmed users to login, in your `UserController`, instead of simply calling `logAttempt( $input )`, call `logAttempt( $input, true )`. The second parameter stands for _"confirmed_only"_.

__My application is crashing since I ran composer update__

*Confide 4.0.0* was a huge update where all the codebase has been rewritten. Some classes changed, the generators has been improved in order to match some better practices (like repositories and separated validator classes). See the _Release Notes_ bellow.

If you have a legacy project that uses an older version of Confide, don't worry. You will be always able to specify a previous version in your `composer.json` file.

For example: `"zizaco/confide": "~3.2"` will avoid composer download version 4.0 but will be able to download bugfixes of version 3.2.

## Release Notes

### Version 4.3.0 Beta 1
* **Username is now an optional field.** Use `--username` when generating the migrations and the controllers.
* General Bugfixes.

### Version 4.2.0
* General Bugfixes.
* Improved README.md.
* Improved existing translations and added new ones.

### Version 4.0.0 RC
* General Bugfixes.
* Improved README.md.
* Confide can use queues for sending email.
* Account confirmation tokens are not time-based anymore.

### [Version 4.0.0 Beta 3](https://github.com/Zizaco/confide/pull/209)
* Now you can customize how long will take for a password reset request to expire (default to 7 hours).
* Reordered validations
* Now all validations are called even if one of them fails. So all validation messages are sent at once.
* `validateIsUnique` method now sends key to attachErrorMsg and also check for errors on each `$identity` field at once

### Version 4.0.0 Beta 2
* UserValidator now adds errors to an existing MessageBag instead of replacing it.
* Password reset token will expire after 7 days.
* Added support for custom connections using the $connection attribute of the model.
* Password reset requests are deleted after being used.

### Version 4.0.0 Beta 1
* Dropped Ardent dependency.
* Updated to support Laravel 4.2
* Dropped support for PHP 5.3
* ConfideUser is going to be a trait+interface from now on.
* Controller generation now also generates an UserRepository class.
* Removed deprecated variables, functions and classes.
* All the codebase has been rewritten.

__Upgrade note:__ A partial update from previous versions is not recommended. In order to upgrade from v3.* to v4.0.0 the best approach is to update the class names in the providers and aliases array, re-generate the user table with the new migration, re-write the "user" class and finally re-generate the controllers. It's very likely any customization made in your codebase will be affected.

### Version 3.0.0
Updated to support Laravel 4.1

### Version 2.0.0 Beta 4
Removed deprecated variable and functions.
* $updateRules
* amend()
* generateUuid
* getUpdateRules
* prepareRules
* getRules
* setUpdateRules
* getUserFromCredsIdentity
* checkUserExists
* isConfirmed

Adds two config values
* login_cache_field (#161)
* throttle_time_period (#162)

### Version 2.0.0 Beta 3
Readme Update

### Version 2.0.0 Beta 2
Pulls in a few pull requests and also locks to Ardent 2.1.x
* Properly handles validation messaging (#124)
* Properly validates in real_save (#110)
* Auth redirect is handled using Redirect::guest instead of a custom session variable (#145)
* Bruteforce vulnerability is addressed. (#151)

### Version 2.0.0 Beta 2
Locked to Ardent 1.1.x

### Version 1.1.0

## Contributing

Feel free to fork this project on [GitHub](https://github.com/Zizaco/confide)

### Coding Standards
When contibuting code to confide, you must follow its coding standards. Confide follows the standard defined in the [PSR-2](http://www.php-fig.org/psr/psr-2/) document.

### Documentation
* Add PHPDoc blocks for all classes, methods, and functions
* Omit the `@return` tag if the method does not return anything
* Add a blank line before `@param`, `@return` and `@throws`

## License

Confide is free software distributed under the terms of the MIT license

## Aditional information

Any questions, feel free to contact me or ask [here](http://forums.laravel.io/viewtopic.php?id=4658)

Any issues, please [report here](https://github.com/Zizaco/confide/issues)
