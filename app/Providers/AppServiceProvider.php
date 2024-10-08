<?php

namespace App\Providers;

use App\Models\ModelFirebase;
use App\Models\ModelMongo;
use App\Repositories\ArticleRepository;
use App\Repositories\ArticleRepositoryImpl;
use App\Repositories\ClientRepository;
use App\Repositories\ClientRepositoryImpl;
use App\Repositories\DetteRepository ;
use App\Repositories\PaiementRepository;
use App\Services\ArticleService;
use App\Services\ArticleServiceImpl;
use App\Services\ClientService;
use App\Services\ClientServiceImpl;
use App\Services\DetteService;
use App\Repositories\DetteRepositoryImpl;
use App\Repositories\FirebaseArchiveRepository;
use App\Repositories\MongoArchiveRepository;
use App\Services\DetteServiceImpl;
use Illuminate\Support\ServiceProvider;
use App\Repositories\PaiementRepositoryImpl;
use App\Services\Dette\ArchiveRepositoryInterface;
use App\Services\FirebaseService;
use App\Services\IMongoDB;
use App\Services\IMongoImpl;
use App\Services\InfoBipSmsService;
use App\Services\SmsProviderInterface;
use App\Services\SmsService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ClientRepository::class, ClientRepositoryImpl::class);
        $this->app->singleton(ClientService::class, ClientServiceImpl::class);

        $this->app->singleton(ArticleRepository::class, ArticleRepositoryImpl::class);
        $this->app->singleton(ArticleService::class, ArticleServiceImpl::class);

        $this->app->singleton(DetteService::class, DetteServiceImpl::class);
        $this->app->singleton(DetteRepository::class, DetteRepositoryImpl::class);

        $this->app->singleton(PaiementRepository::class, PaiementRepositoryImpl::class);


        $this->app->singleton(SmsProviderInterface::class, function ($app) {
            $service = env('SMS_SERVICE','twilio');
            if ($service === 'infobip') {
                return new InfoBipSmsService();
            }
            return new SmsService();
        });

        $this->app->bind(ArchiveRepositoryInterface::class, function ($app) {
            $archiveType = config('services.archive.type');
            
            if ($archiveType === 'mongodb') {
                return new MongoArchiveRepository(new ModelMongo());
            } elseif ($archiveType === 'firebase') {
                return new FirebaseArchiveRepository(new ModelFirebase());
            }
            
            throw new \Exception("Invalid archive type specified");
        });

        $this->app->singleton('MongoClient', function () {
            return new IMongoImpl();
        });

        $this->app->singleton('FirebaseClient', function () {
            return new FirebaseService();
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
