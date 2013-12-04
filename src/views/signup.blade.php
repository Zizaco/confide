@extends(Config::get('confide::views.layout'))

@section('title')
<h1>Confide - Create User</h1>
@stop

@section('content')
{{ Form::open(array(
    'url' =>  Confide::checkAction('UserController@store') ?: URL::to('user'),
    'method' => 'POST',
    'accept-charset' => 'UTF-8'
)) }}
    {{ Form::hidden('_token', Session::getToken()) }}
    <fieldset>
        <div class="form-group">
            {{ Form::label('username', Lang::get('confide::confide.username')) }}
            {{ Form::text('username', Input::old('username'), array(
                'class' => 'form-control',
                'id' => 'username',
                'placeholder' => Lang::get('confide::confide.username'),
                'tabindex' => '1'
            )) }}
        </div>
        <div class="form-group">
            {{ HTML::decode(Form::label(
                'email',
                Lang::get('confide::confide.e_mail').' <small>('.Lang::get('confide::confide.signup.confirmation_required').')</small>'
            )) }}
            {{ Form::email('email', Input::old('email'), array(
                'class' => 'form-control',
                'id' => 'email',
                'placeholder' => Lang::get('confide::confide.e_mail'),
                'tabindex' => '2'
            )) }}
        </div>
        <div class="form-group">
            {{ Form::label('password', Lang::get('confide::confide.password')) }}
            {{ Form::password('password', array(
                'class' => 'form-control',
                'id' => 'password',
                'placeholder' => Lang::get('confide::confide.password'),
                'tabindex' => '3'
            )) }}
        </div>
        <div class="form-group">
            {{ Form::label('password_confirmation', Lang::get('confide::confide.password_confirmation')) }}
            {{ Form::password('password_confirmation', array(
                'class' => 'form-control',
                'id' => 'password_confirmation',
                'placeholder' => Lang::get('confide::confide.password_confirmation'),
                'tabindex' => '4'
            )) }}
        </div>

        @if ( Session::get('error') )
            <div class="alert alert-error alert-danger">
                @if ( is_array(Session::get('error')) )
                    {{ head(Session::get('error')) }}
                @endif
            </div>
        @endif

        @if ( Session::get('notice') )
            <div class="alert">{{ Session::get('notice') }}</div>
        @endif

        <div class="form-actions form-group">
            {{ Form::submit(Lang::get('confide::confide.signup.submit'), array(
                'class' => 'btn btn-primary',
                'tabindex' => '5'
            )) }}
        </div>
    </fieldset>
{{ Form::close() }}
@stop
