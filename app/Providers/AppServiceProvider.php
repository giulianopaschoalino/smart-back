<?php

namespace App\Providers;

use App\Repositories\AboutUs\AboutUsContractInterface;
use App\Repositories\AboutUs\AboutUsRepository;
use App\Repositories\DadosCadastrais\DadosCadastraisContractInterface;
use App\Repositories\DadosCadastrais\DadosCadastraisRepository;
use App\Repositories\DadosTe\DadosTeContractInterface;
use App\Repositories\DadosTe\DadosTeRepository;
use App\Repositories\Economy\EconomyContractInterface;
use App\Repositories\Economy\EconomyRepository;
use App\Repositories\Faqs\FaqContractInterface;
use App\Repositories\Faqs\FaqRepository;
use App\Repositories\Med5min\Med5minContractInterface;
use App\Repositories\Med5min\Med5minRepository;
use App\Repositories\Notifications\NotificationContractInterface;
use App\Repositories\Notifications\NotificationRepository;
use App\Repositories\Pld\PldContractInterface;
use App\Repositories\Pld\PldRepository;
use App\Repositories\Users\UserContractInterface;
use App\Repositories\Users\UserRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {

        setlocale(LC_TIME,  config('app.locale'), 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

        $this->app->bind(
            UserContractInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            NotificationContractInterface::class,
            NotificationRepository::class
        );
        $this->app->bind(
            FaqContractInterface::class,
            FaqRepository::class
        );
        $this->app->bind(
            PldContractInterface::class,
            PldRepository::class
        );
        $this->app->bind(
            EconomyContractInterface::class,
            EconomyRepository::class
        );
        $this->app->bind(
            DadosTeContractInterface::class,
            DadosTeRepository::class
        );
        $this->app->bind(
            DadosCadastraisContractInterface::class,
            DadosCadastraisRepository::class
        );
        $this->app->bind(
          Med5minContractInterface::class,
            Med5minRepository::class
        );
        $this->app->bind(
            AboutUsContractInterface::class,
            AboutUsRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        setlocale(LC_TIME,  config('app.locale'), 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    }
}
