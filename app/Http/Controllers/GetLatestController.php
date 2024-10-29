<?php

namespace App\Http\Controllers;

use App\Models\ReportCustom;
use Illuminate\Http\Request;

class GetLatestController extends Controller
{
    public function updateLiveStatus(Request $request, ReportCustom $report){
      $report->update([
        'get_latest' => $request->getLatest
      ]);

      $message = $report->report->name . ' live updates turn '. ($request->getLatest ? 'ON.' : 'OFF.');

      return response()->json(['data' => $request->getLatest, 'success' => $message]);
    }
}
