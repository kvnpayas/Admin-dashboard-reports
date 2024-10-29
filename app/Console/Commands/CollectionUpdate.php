<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\CollectionReportLocation;
use App\Events\CollectionUpdate as CollectionLocation;

class CollectionUpdate extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:collection-update';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Collection Location';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $fetchData = CollectionReportLocation::all();

    $header = [
      'PAYMENT CENTER - BDO',
      'PAYMENT CENTER - EASYPAY',
      'PAYMENT CENTER - METROBANK',
      'PAYMENT CENTER - CITYWALK',
      'PAYMENT CENTER - MAGIC',
      'PAYMENT CENTER - MAIN',
      'PAYMENT CENTER - MATMAGIC',
      'PAYMENT CENTER - METRO',
      'PAYMENT CENTER - MARKTCTY',
      'PAYMENT CENTER - SM',
    ];

    $dataHeader = array_map(function ($item) {
      return str_replace('PAYMENT CENTER -', 'TARLAC ELECTRIC', $item);
    }, $header);

    $results = $fetchData->groupBy('collection_date')->toArray();
    foreach ($results as $date => $data) {
      $groupLocation = $data->groupBy('location')->toArray();
      $results[$date] = [];
      foreach ($dataHeader as $headerDetail) {
        $results[$date][$headerDetail] = [
          'amount' => isset($groupLocation[$headerDetail]) ? collect($groupLocation[$headerDetail])->sum('amount_paid') : null,
          'account_count' => isset($groupLocation[$headerDetail]) ? collect($groupLocation[$headerDetail])->sum('account_count') : null
        ];
      }
    }

    broadcast(new CollectionLocation(['data' => $results, 'headers' => $header]));
  }
}
