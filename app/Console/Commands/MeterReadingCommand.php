<?php

namespace App\Console\Commands;

use App\Events\MeterReadingEvent;
use App\Models\MeterReading;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MeterReadingCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:meter-reading-command';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Meter Reading update';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    Log::info('Starting MeterReading job');

    $fetchData = MeterReading::all();

    broadcast(new MeterReadingEvent(['data' => $fetchData]));
    Log::info('Completed MeterReading job');
  }
}
