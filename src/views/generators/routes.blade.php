//
@if (! $restful)

// Confide routes
Route::get('{{ $url }}/create', '{{ $controllerName }}@create');
Route::post('{{ $url }}', '{{ $controllerName }}@store');
Route::get('{{ $url }}/login', '{{ $controllerName }}@login');
Route::post('{{ $url }}/login', '{{ $controllerName }}@do_login');
Route::get('{{ $url }}/confirm/{code}', '{{ $controllerName }}@confirm');
Route::get('{{ $url }}/forgot_password', '{{ $controllerName }}@forgot_password');
Route::post('{{ $url }}/forgot_password', '{{ $controllerName }}@do_forgot_password');
Route::get('{{ $url }}/reset_password/{token}', '{{ $controllerName }}@reset_password');
Route::post('{{ $url }}/reset_password', '{{ $controllerName }}@do_reset_password');
Route::get('{{ $url }}/logout', '{{ $controllerName }}@logout');
@else

// Confide RESTful route
Route::get('{{ $url }}/confirm/{code}', '{{ $controllerName }}@getConfirm');
Route::get('{{ $url }}/reset_password/{token}', '{{ $controllerName }}@getReset');
Route::get('{{ $url }}/reset_password', '{{ $controllerName }}@postReset');
Route::controller( '{{ $url }}', '{{ $controllerName }}');
@endif
