<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            @if(Session::get('error'))
                <div class="alert alert-danger" role="alert">
                    {{ Session::get('error') }}
                </div>
            @endif

            @if(Session::get('notice'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('notice') }}
                </div>
            @endif
            
            {{ Form::open(array('url' => 'users/login', 'class' => 'form-horizontal')) }}

                <div class="form-group">
                    {{ Form::label('email', Lang::get('confide::confide.username_e_mail'), array('class' => 'col-sm-2 control-label')) }}
                    <div class="col-sm-10">
                        {{ Form::text('email', null, array('class' => 'form-control', 'placeholder' => Lang::get('confide::confide.username_e_mail'))) }}
                    </div>
                </div>

                <div class="form-group">
                    {{ Form::label('password', Lang::get('confide::confide.password'), array('class' => 'col-sm-2 control-label')) }}
                    <div class="col-sm-10">
                        {{ Form::password('password', array('class' => 'form-control', 'placeholder' => Lang::get('confide::confide.password'))) }}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2 text-right">
                        <div class="checkbox checkbox-inline">
                            {{ Form::hidden('remember', '0') }}
                            <label>{{ Form::checkbox('remember', '1') }} Lang::get('confide::confide.login.remember')</label>
                        </div>
                        <div class="text-right checkbox-inline">
                            {{ Form::submit(Lang::get('confide::confide.login.submit'), array('class' => 'btn btn-success')) }}
                        </div>
                    </div>
                </div>
                
            {{ Form::close() }}

            <br>

            <small><a href="{{{ URL::to('users/forgot_password') }}}">{{{ Lang::get('confide::confide.login.forgot_password') }}}</a></small>

        </div>
    </div>
</div>