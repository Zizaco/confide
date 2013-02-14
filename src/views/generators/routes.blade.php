{{{ "\n\n" }}}
@if (! $restful)
// Confide Routes
Route::get( '{{ lcfirst(substr($name,0,-10)) }}/create',          '{{ $name }}@create');
Route::post('{{ lcfirst(substr($name,0,-10)) }}',                 '{{ $name }}@store');
Route::get( '{{ lcfirst(substr($name,0,-10)) }}/login',           '{{ $name }}@login');
Route::post('{{ lcfirst(substr($name,0,-10)) }}/login',           '{{ $name }}@do_login');
Route::get( '{{ lcfirst(substr($name,0,-10)) }}/confirm/{code}',  '{{ $name }}@confirm');
Route::get( '{{ lcfirst(substr($name,0,-10)) }}/forgot_password', '{{ $name }}@forgot_password');
Route::post('{{ lcfirst(substr($name,0,-10)) }}/reset_password',  '{{ $name }}@reset_password');
Route::get( '{{ lcfirst(substr($name,0,-10)) }}/logout',          '{{ $name }}@logout');
@else
Route::controller( 'user', '{{ $name }}');
@endif
