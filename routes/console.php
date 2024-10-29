<?php

use App\Console\Commands\CollectionPaymentCenterCommand;
use App\Console\Commands\CollectionSummaryUpdate;
use App\Console\Commands\CollectionUpdate;
use App\Console\Commands\CustomerStatus;
use App\Console\Commands\MeterReadingCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Schedule::command(CustomerStatus::class)->everyTenSeconds();
Schedule::command(CollectionUpdate::class)->everyTenSeconds();
Schedule::command(CollectionSummaryUpdate::class)->everyTenSeconds();
Schedule::command(CollectionPaymentCenterCommand::class)->everyTenSeconds();
Schedule::command(MeterReadingCommand::class)->everyTenSeconds();
