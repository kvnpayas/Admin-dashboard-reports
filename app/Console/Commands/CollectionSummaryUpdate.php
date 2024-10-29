<?php

namespace App\Console\Commands;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\CollectionSummaryCrosstab;
use App\Models\CollectionSummaryCrosstab as Collection;

class CollectionSummaryUpdate extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:collection-summary-update';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Collection Summary Crosstab Update';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    // $currentDate = Carbon::now()->format('Y-m-d');
    Log::info('Starting CollectionSummaryCrosstab job');
    $fetchData = Collection::all();
    $uniqueTags = $fetchData->pluck('tag')->unique();
    $uniquePayType = $fetchData->pluck('pay_type')->unique();
    $results = [];
    foreach ($uniquePayType as $payType) {
      foreach ($fetchData as $data) {
        if ($payType == $data->pay_type) {
          $results[$payType][] = [
            'tag' => $data->tag,
            'collected_amount' => $data->collected_amount,
          ];
        }
      }
    }

    broadcast(new CollectionSummaryCrosstab(['data' => $results, 'header' => $uniqueTags]));
    Log::info('Completed CollectionSummaryCrosstab job');
  }
}
