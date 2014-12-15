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
            
            {{ Form::open(array('url' => 'users/reset_password', 'class' => 'form-horizontal')) }}

                {{ Form::hidden('token', $token) }}

                <div class="form-group">
                    {{ Form::label('password', Lang::get('confide::confide.password'), array('class' => 'col-sm-2 control-label')) }}
                    <div class="col-sm-10">
                        <div class="row">
                            <div class="col-sm-6">
                                {{ Form::password('password', array('class' => 'form-control', 'placeholder' => Lang::get('confide::confide.password'))) }}
                            </div>
                            <div class="col-sm-6">
                                {{ Form::password('password_confirmation', array('class' => 'form-control', 'placeholder' => Lang::get('confide::confide.password_confirmation'))) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2 text-right">
                        {{ Form::submit(Lang::get('confide::confide.forgot.submit'), array('class' => 'btn btn-success')) }}
                    </div>
                </div>
                
            {{ Form::close() }}

        </div>
    </div>
</div>