<?php

namespace App\Http\Controllers;

use App\Models\MeterReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MeterreadingController extends Controller
{
  public function getMeterReading(Request $request)
  {
    if (date('Y-m-d') == $request->date) {

      try {
        $fetchdata = MeterReading::all();
      } catch (\Exception $e) {

        dd($e->getMessage());
      }
    } else {
      $fetchDataRpt1 = DB::connection('icss_db')->select("execute dbo.sp_meterreading_summary_rpt;1 @RunDate = ?", [$request->date]);
      $fetchDataRpt2 = DB::connection('icss_db')->select("execute dbo.sp_meterreading_summary_rpt_2;1 @RunDate = ?", [$request->date]);
      $fetchdata = array_merge($fetchDataRpt1, $fetchDataRpt2);
    }

    return response()->json(['data' => $fetchdata]);

  }
}
