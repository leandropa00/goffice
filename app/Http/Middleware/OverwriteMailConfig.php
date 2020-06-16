<?php

namespace App\Http\Middleware;

use App\Setting;
use App\SmtpSetting;
use Closure;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class OverwriteMailConfig
{
    private $smtpSetting;
    private $pushSetting;
    private $settings;

    public function __construct()
    {
        $this->smtpSetting = SmtpSetting::first();
        $this->pushSetting = push_setting();
        $this->settings = $this->global ?? Setting::first();

    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $smtpSetting = $this->smtpSetting;
        $pushSetting = $this->pushSetting;
        $settings = $this->settings;

        if(\config('app.env') !== 'development'){
            Config::set('mail.driver', $smtpSetting->mail_driver);
            Config::set('mail.host', $smtpSetting->mail_host);
            Config::set('mail.port', $smtpSetting->mail_port);
            Config::set('mail.username', $smtpSetting->mail_username);
            Config::set('mail.password', $smtpSetting->mail_password);
            Config::set('mail.encryption', $smtpSetting->mail_encryption);
        }

        Config::set('mail.from.name', $smtpSetting->mail_from_name);
        Config::set('mail.from.address', $smtpSetting->mail_from_email);
        Config::set('services.onesignal.app_id', $pushSetting->onesignal_app_id);
        Config::set('services.onesignal.rest_api_key', $pushSetting->onesignal_rest_api_key);
        Config::set('app.name', $settings->company_name);
        Config::set('app.logo', $settings->logo_url);

        $app = App::getInstance();
        $app->register('Illuminate\Mail\MailServiceProvider');
        return $next($request);
    }
}
