@extends(Config::get('confide::views.layout'))

@section('title')
<h1>Confide - Reset Password</h1>
@stop

@section('content')
{{ Form::open(array(
    'url' =>  Confide::checkAction('UserController@do_reset_password') ?: URL::to('/user/reset_password'),
    'method' => 'POST',
    'accept-charset' => 'UTF-8'
)) }}
    {{ Form::hidden('token', $token) }}
    {{ Form::hidden('_token', Session::getToken()) }}

    <div class="form-group">
        {{ Form::label('password', Lang::get('confide::confide.password')) }}
        {{ Form::password('password', array(
            'class' => 'form-control',
            'id' => 'password',
            'placeholder' => Lang::get('confide::confide.password'),
            'tabindex' => '1'
        )) }}
    </div>
    <div class="form-group">
        {{ Form::label('password_confirmation', Lang::get('confide::confide.password_confirmation')) }}
        {{ Form::password('password_confirmation', array(
            'class' => 'form-control',
            'id' => 'password_confirmation',
            'placeholder' => Lang::get('confide::confide.password_confirmation'),
            'tabindex' => '2'
        )) }}
    </div>

    @if ( Session::get('error') )
        <div class="alert alert-error alert-danger">{{{ Session::get('error') }}}</div>
    @endif

    @if ( Session::get('notice') )
        <div class="alert">{{{ Session::get('notice') }}}</div>
    @endif

    <div class="form-actions form-group">
        {{ Form::submit(Lang::get('confide::confide.forgot.submit'), array(
            'class' => 'btn btn-primary',
            'tabindex' => '3'
        )) }}
    </div>
{{ Form::close() }}
@stop
