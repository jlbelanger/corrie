<?php

namespace App\Providers;

use App\Http\Kernel;
use App\Models\Person;
use App\Observers\PersonObserver;
use DB;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Log;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Registers any application services.
	 *
	 * @return void
	 */
	public function register()
	{
	}

	/**
	 * Bootstraps any application services.
	 *
	 * @param  Kernel $kernel
	 * @return void
	 */
	public function boot(Kernel $kernel)
	{
		if (config('app.debug')) {
			if (config('logging.database')) {
				DB::listen(function ($query) {
					Log::info($query->sql, $query->bindings, $query->time);
				});
			}
		}

		if ($this->app->environment() !== 'local') {
			$kernel->appendMiddlewareToGroup('api', \Illuminate\Routing\Middleware\ThrottleRequests::class);
		}

		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed
		ResetPassword::createUrlUsing(function ($notifiable, string $token) {
			return config('app.admin_url') . '/reset-password/' . $token;
		});

		Person::observe(PersonObserver::class);
	}
}
