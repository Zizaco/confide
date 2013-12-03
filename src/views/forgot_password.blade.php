{{ Form::open(array(
    'url' =>  Confide::checkAction('UserController@do_forgot_password') ?: URL::to('/user/forgot_password'),
    'method' => 'POST',
    'accept-charset' => 'UTF-8'
)) }}
    {{ Form::hidden('_token', Session::getToken()) }}

    <div class="form-group">
        {{ Form::label('email', Lang::get('confide::confide.e_mail')) }}
        {{ Form::email('email', Input::old('email'), array(
            'class' => 'form-control',
            'id' => 'email',
            'placeholder' => Lang::get('confide::confide.e_mail'),
            'tabindex' => '1'
        )) }}
        <div class="input-append input-group">
            <span class="input-group-btn">
                {{ Form::submit(Lang::get('confide::confide.forgot.submit'), array(
                    'class' => 'btn btn-default',
                    'tabindex' => '2'
                )) }}
            </span>
        </div>
    </div>

    @if ( Session::get('error') )
        <div class="alert alert-error alert-danger">{{{ Session::get('error') }}}</div>
    @endif

    @if ( Session::get('notice') )
        <div class="alert">{{{ Session::get('notice') }}}</div>
    @endif
{{ Form::close() }}
