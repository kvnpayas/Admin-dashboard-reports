<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Reports;
use App\Models\ReportRow;
use App\Models\ReportCustom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportCustomController extends Controller
{
  public function index()
  {
    // $customReport = Auth::user()->with('reportRows.reportCols')->get();
    $customReport = ReportRow::where('user_id', Auth::user()->id)->with('reportCols')->get();
    foreach ($customReport as $custom) {
      $custom->reportCols->map(function ($item) {
        $item->name = $item->report->name;
        $item->max_col = $item->report->max_col; 
        return $item;
      });
    }
    return Inertia::render('CustomReport', ['customReport' => $customReport]);
  }

  public function getReports()
  {
    $reports = Auth::user()->group->reports;
    return response()->json($reports);
  }

  public function save(Request $request)
  {

    $dataExist = ReportRow::where('user_id', Auth::user()->id)->with('reportCols')->first();

    if($dataExist){
      ReportRow::where('user_id', Auth::user()->id)->delete();
    }
    $rowCount = 1;
    foreach ($request->request as $data) {
      $row = ReportRow::create([
        'user_id' => Auth::user()->id,
        'row' => $rowCount,
        'col' => $data['col'],
        'add' => $data['add'],
      ]);

      foreach ($data['report_cols'] as $report) {
        ReportCustom::create([
          'report_id' => $report['report_id'],
          'row_id' => $row->id,
          'span' => $report['span'],
        ]);
      }
      $rowCount++;
    }
    return response()->json(['success' => 'Report layout saved!']);
  }
}
