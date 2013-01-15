# Confide (Laravel4 Package)

![Confide Poster](https://dl.dropbox.com/u/12506137/libs_bundles/confide.png)

Confide is a authentication solution for **Laravel4** made to eliminate repetitive tasks involving the management of users: Account creation, login, logout, confirmation by e-mail, and other password reset.

Confide aims to be simple to use, quick to configure and flexible.

## Features

**Current:**
- Account confirmation (through confirmation link).
- Password reset (sending email with new password).
- Easily render forms for login, signup and password reset.
- Generate customizable routes for login, signup, password reset, confirmation, etc.
- Generate a customizable controller that handles the basic user account actions
- Contains a set of methods to help basic user features.
- Integrated with the Laravel Auth component/configs.

**Planned:**
- Captcha in user signup and password reset.
- General improvements.

## Quick start

### Required setup

In the `require` key of `composer.json` file add the following

    "zizaco/confide": "v0.4beta"

Run the Composer update comand

    $ composer update

In your `config/app.php` add `'Zizaco\Confide\ConfideServiceProvider'` to the end of the `$providers` array

    'providers' => array(

        'Illuminate\Foundation\Providers\ArtisanServiceProvider',
        'Illuminate\Auth\AuthServiceProvider',
        ...
        'Zizaco\Confide\ConfideServiceProvider',

    ),

At the end of `config/app.php` add `'Confide'    => 'Zizaco\Confide\ConfideFacade'` to the `$aliases` array

    'aliases' => array(

        'App'        => 'Illuminate\Support\Facades\App',
        'Artisan'    => 'Illuminate\Support\Facades\Artisan',
        ...
        'Confide'    => 'Zizaco\Confide\ConfideFacade',

    ),

### Configuration

Set the properly values to the `config/auth.php`. This values will be used by confide to generate the database migration and to generate controllers and routes.

Set the `address` and `name` from the `from` array in `config/mail.php`. Those will be used to send account confirmation and password reset emails to the users.

### User model

Now generate the Confide migration

    $ php artisan confide:migration

It will generate the `<timestamp>_confide_setup_users_table.php` migration. You may now run it with the artisan migrate command:

    $ php artisan migrate

It will setup a table containing `username`, `email`, `password`, `confirmation_code` and `confirmed` fields, which are the default fields needed for Confide use. Feel free to add more fields to the database.

Change your User model in `app/models/User.php` to:

    <?php

    use Zizaco\Confide\ConfideUser;

    class User extends ConfideUser {

    }

`ConfideUser` class will take care of some behaviors of the user model.

### Dump the default acessors

Least, you can dump a default controller and the default routes for Confide.

    $ php artisan confide:controller
    $ php artisan confide:routes

Don't forget to dump composer autoload

    $ composer dump-autoload

And you are ready to go. Access `http://yourapp/user/create` to create your first user. Check the `app/routes.php` to see the available routes.

## Usage in detail

**Basic setup:**

1. Database connection in `config/database.php` running properly.
2. Correct model and table names in `config/auth.php`. They will be used by Confide all the time.
3. `from` configuration in `config/mail.php`.

**Aditional steps:**

1. `ConfideServiceProvider` and `ConfideFacade` entry in `config/app.php` `'providers'` and `'aliases'` respectively.
2. User model (with the same name as in `config/auth.php`) should extend `ConfideUser` class. This will cause to methods like `resetPassword()`, `confirm()` and a overloaded `save()` to be available.

**Optional steps:**

1. Use `Confide` facade to dump login and signup forms easly with `makeLoginForm()` and `makeSignupForm()`. You can render the forms within your views by doing `{{ Confide::makeLoginForm()->render() }}`.
2. Generate a controller with the template contained in Confide throught the artisan command `$ php artisan confide:controller`. If a controller with the same name exists it will **NOT** be overwritten.
3. Generate routes matching the controller template throught the artisan command `$ php artisan confide:routes`. Your `routes.php` will **NOT** be overwritten.

### Advanced

You can change the model name that will be authenticated in the `config/auth.php` file.
Confide uses the values present in that configuration file.

To change the controller name when dumping the default controller template you can use the --name option.

    $ php artisan confide::controller --name Employee

Will result in `EmployeeController`

Then, when dumping the routes, you should use the --controller option to match the existing controller.

    $ php artisan confide::routes --controller Employee


## License

Confide is free software distributed under the terms of the MIT license

## Aditional information

Any questions, feel free to contact me.
