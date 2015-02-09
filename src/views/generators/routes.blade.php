//
@if (! $restful)

// Confide routes
Route::get('{{ $url }}/create', '{{ $controllerName }}@create');
Route::post('{{ $url }}', '{{ $controllerName }}@store');
Route::get('{{ $url }}/login', '{{ $controllerName }}@login');
Route::post('{{ $url }}/login', '{{ $controllerName }}@doLogin');
Route::get('{{ $url }}/confirm/{code}', '{{ $controllerName }}@confirm');
Route::get('{{ $url }}/forgot_password', '{{ $controllerName }}@forgotPassword');
Route::post('{{ $url }}/forgot_password', '{{ $controllerName }}@doForgotPassword');
Route::get('{{ $url }}/reset_password/{token}', '{{ $controllerName }}@resetPassword');
Route::post('{{ $url }}/reset_password', '{{ $controllerName }}@doResetPassword');
Route::get('{{ $url }}/logout', '{{ $controllerName }}@logout');
@else

// Confide RESTful route
Route::get('{{ $url }}/confirm/{code}', '{{ $controllerName }}@getConfirm');
Route::get('{{ $url }}/reset_password/{token}', '{{ $controllerName }}@getReset');
Route::post('{{ $url }}/reset_password', '{{ $controllerName }}@postReset');
Route::controller( '{{ $url }}', '{{ $controllerName }}');
@endif
