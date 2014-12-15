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

            {{ Form::open(array('url' => 'users/forgot_password', 'class' => 'form-horizontal')) }}

                <div class="form-group">
                    {{ Form::label('email', Lang::get('confide::confide.e_mail'), array('class' => 'col-sm-2 control-label')) }}
                    <div class="col-sm-10">
                        <div class="input-append input-group">
                            {{ Form::text('email', null, array('placeholder' => Lang::get('confide::confide.e_mail'), 'class' => 'form-control')) }}
                            <span class="input-group-btn">
                                {{ Form::submit(Lang::get('confide::confide.forgot.submit'), array('class' => 'btn btn-default')) }}
                            </span>
                        </div>
                    </div>
                </div>

            {{ Form::close() }}

        </div>
    </div>
</div>