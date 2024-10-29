<?php

use App\Http\Middleware\AdminAccess;
use App\Http\Middleware\GuestUser;
use Illuminate\Foundation\Application;
use Illuminate\Auth\Events\Authenticated;
use App\Http\Middleware\AuthenticatedUser;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
      HandleInertiaRequests::class,
    ]);
    $middleware->alias([
      'auth' => AuthenticatedUser::class,
      'guest' => GuestUser::class,
      'admin' => AdminAccess::class,
    ]);
  })
  ->withExceptions(function (Exceptions $exceptions) {
    //
  })->create();
