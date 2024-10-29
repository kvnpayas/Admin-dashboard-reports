<?php

namespace App\Console\Commands;

use App\Events\CollectionPaymentCenterEvent;
use App\Models\CollectionPaymentCenter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CollectionPaymentCenterCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:collection-payment-center-command';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Collection Payment Center Update';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    Log::info('Starting CollectionPaymentCenter job');
    $fetchData = CollectionPaymentCenter::all();
    broadcast(new CollectionPaymentCenterEvent(['data' => $fetchData]));
    Log::info('Completed CollectionPaymentCenter job');
  }
}
