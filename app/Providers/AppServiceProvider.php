<?php

namespace App\Providers;

use App\Support\SmtpRuntime;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Aplica as credenciais de SMTP cadastradas no portal (sobre o .env).
        // Protegido para não quebrar antes das migrations / sem banco.
        try {
            if (Schema::hasTable('configuracoes')) {
                SmtpRuntime::aplicar();
            }
        } catch (\Throwable) {
            // banco indisponível — segue com o mailer padrão do .env
        }
    }
}
