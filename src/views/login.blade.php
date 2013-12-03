@extends(Config::get('confide::views.layout'))

@section('title')
<h1>Confide - Login</h1>
@stop

@section('content')
{{ Form::open(array(
    'url' =>  Confide::checkAction('UserController@do_login') ?: URL::to('/user/login'),
    'method' => 'POST',
    'accept-charset' => 'UTF-8'
)) }}
    <fieldset>
        <div class="form-group">
            {{ Form::label('email', Lang::get('confide::confide.username_e_mail')) }}
            {{ Form::text('email', Input::old('email'), array(
                'class' => 'form-control',
                'id' => 'email',
                'placeholder' => Lang::get('confide::confide.username_e_mail'),
                'tabindex' => '1'
            )) }}
        </div>
        <div class="form-group">
            {{ Form::forgotPasswordLabel('password', array(
                'class' => 'form-control',
                'id' => 'password'
            )) }}
            {{ Form::password('password', array(
                'class' => 'form-control',
                'id' => 'password',
                'placeholder' => Lang::get('confide::confide.password'),
                'tabindex' => '2'
            )) }}
        </div>
        <div class="form-group">
            {{ Form::hidden('remember', '0') }}
            {{ Form::label('remember', Lang::get('confide::confide.login.remember')) }}
            {{ Form::checkbox('remember', '1', false, array(
                'tabindex' => '3'
            )) }}
        </div>
        @if ( Session::get('error') )
            <div class="alert alert-error">{{{ Session::get('error') }}}</div>
        @endif

        @if ( Session::get('notice') )
            <div class="alert">{{{ Session::get('notice') }}}</div>
        @endif
        <div class="form-group">
            {{ Form::submit(Lang::get('confide::confide.login.submit'), array(
                'class' => 'btn btn-primary',
                'tabindex' => '4'
            )) }}
        </div>
    </fieldset>
{{ Form::close() }}
@stop
