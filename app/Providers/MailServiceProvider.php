<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MailServiceProvider extends ServiceProvider
{
   public function boot()
{
    $this->app->booted(function () {
        try {
            $host = request()->getHost();
            $siteConfigPath = base_path("website-configs/{$host}/database.php");

            if (!file_exists($siteConfigPath)) {
                Log::warning("MailServiceProvider: DB config not found for host: {$host}");
                return;
            }

            // Load the DB config
            $databaseConfig = include $siteConfigPath;
            // Override Laravel DB connection
            Config::set('database.connections.mysql', $databaseConfig['connections']['mysql']);
            DB::purge('mysql');
            DB::reconnect('mysql');
            DB::connection()->getPdo(); // This will throw an exception if connection fails
            // Log::info("Database connection successful.");


            // Fetch SMTP settings from DB
            $smtpSettings = DB::connection('mysql')->table('general_settings')
                ->whereIn('name', [
                    'smtp_host',
                    'smtp_port',
                    'smtp_username',
                    'smtp_password',
                    'smtp_encrpt',
                    'smtp_email',
                    'smtp_from_name'
                ])
                ->pluck('value', 'name');

            // Fallback to env if DB values missing
            $smtpHost       = $smtpSettings['smtp_host'] ?? env('MAIL_HOST');
            $smtpPort       = $smtpSettings['smtp_port'] ?? env('MAIL_PORT');
            $smtpUser       = $smtpSettings['smtp_username'] ?? env('MAIL_USERNAME');
            $smtpPass       = $smtpSettings['smtp_password'] ?? env('MAIL_PASSWORD');
            $smtpEncryption = $smtpSettings['smtp_encrpt'] ?? env('MAIL_ENCRYPTION');
            $smtpFromEmail  = $smtpSettings['smtp_email'] ?? env('MAIL_FROM_ADDRESS');
            $smtpFromName   = $smtpSettings['smtp_from_name'] ?? env('MAIL_FROM_NAME');

            // Apply to mail config
            Config::set('mail.mailers.smtp.host', $smtpHost);
            Config::set('mail.mailers.smtp.port', $smtpPort);
            Config::set('mail.mailers.smtp.username', $smtpUser);
            Config::set('mail.mailers.smtp.password', $smtpPass);
            Config::set('mail.mailers.smtp.encryption', $smtpEncryption);
            Config::set('mail.from.address', $smtpFromEmail);
            Config::set('mail.from.name', $smtpFromName);

        } catch (\Throwable $e) {
            Log::error("MailServiceProvider Error: " . $e->getMessage());
        }
    });
}


    public function register()
{
    $this->app->booting(function () {
        $host = request()->getHost();
        $siteConfigPath = base_path("website-configs/{$host}/database.php");

        if (file_exists($siteConfigPath)) {
            $databaseConfig = include $siteConfigPath;
            Config::set('database.connections.mysql', $databaseConfig['connections']['mysql']);
        }
    });
}

}
