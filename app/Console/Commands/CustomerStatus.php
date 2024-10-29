<?php

namespace App\Console\Commands;

use App\Models\CustomerStatus as Customer;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\CustomerStatusUpdated;

class CustomerStatus extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:customer-status';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Update data from customer';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    Log::info('Starting CustomerStatusUpdated job');
    // Your job logic here

    $fetchData = Customer::all();

    $accounts = $fetchData->whereIn('tag', ['Active Accounts', 'Connected', 'Disconnected'])->toArray();
    $accountsParam = $fetchData->whereNotIn('tag', ['Active Accounts', 'Connected', 'Disconnected'])->toArray();

    broadcast(new CustomerStatusUpdated(['accounts' => array_values($accounts), 'accountsParam' => array_values($accountsParam)]));
    Log::info('Completed CustomerStatusUpdated job');
  }
}
