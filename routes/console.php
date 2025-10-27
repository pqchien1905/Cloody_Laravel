<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Lên lịch dọn dẹp thùng rác hàng ngày lúc 02:00
Schedule::command('trash:cleanup')->dailyAt('02:00');
