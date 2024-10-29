<?php

namespace App\Http\Controllers;

use App\Models\CollectionPaymentCenter;
use App\Models\CollectionReportLocation;
use App\Models\CollectionSummaryCrosstab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
  public function getCollectionReportLocation(Request $request)
  {
    if (date('Y-m-d') == $request->dateTo) {
      $fetchData = CollectionReportLocation::all();
    } else {
      $fetchData = DB::connection('icss_db')->select("EXEC dbo.sp_collection_report_per_date_account_count @collector_code = ?, @cashier_code = ?, @fromdate = ?, @todate = ?", [
        'TEI', 
        '%', 
        $request->dateFrom, 
        $request->dateTo 
      ]);
    }

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

    $results = collect($fetchData)->groupBy('collection_date')->toArray();
    foreach ($results as $date => $data) {
      // $results[$date][] = collect($data)->groupBy('location');
      $groupLocation = collect($data)->groupBy('location')->toArray();
      $results[$date] = [];
      foreach ($dataHeader as $headerDetail) {
        $results[$date][$headerDetail] = [
          'amount' => isset($groupLocation[$headerDetail]) ? collect($groupLocation[$headerDetail])->sum('amount_paid') : null,
          'account_count' => isset($groupLocation[$headerDetail]) ? collect($groupLocation[$headerDetail])->sum('account_count') : null
        ];
      }
    }
    return response()->json(['data' => $results, 'headers' => $header]);
  }

  public function getCollectionSummaryCross(Request $request)
  {
    if ($request->date) {
      $fetchData = DB::connection('icss_db')->select("execute dbo.sp_collection_summary_crosstab;1 @RunDate = ?", [$request->date]);
      $uniqueTags = collect($fetchData)->pluck('tag')->unique()->toArray();
      $uniquePayType = collect($fetchData)->pluck('pay_type')->unique()->toArray();
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
      return response()->json(['data' => $results, 'header' => $uniqueTags]);
    } else {
      $fetchData = CollectionSummaryCrosstab::all();
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
      return response()->json(['data' => $results, 'header' => $uniqueTags]);
    }
  }
  public function getCollectionSummaryPayment(Request $request)
  {
    if ($request->date) {
      $fetchData = DB::connection('icss_db')->select("sp_collection_summary_paymentcenter_crosstab;1 @RunDate = ?", [$request->date]);
    } else {
      $fetchData = CollectionPaymentCenter::all();
    }
    return response()->json(['data' => $fetchData]);
  }

}
