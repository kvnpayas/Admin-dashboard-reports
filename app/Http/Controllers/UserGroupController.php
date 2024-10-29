<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Reports;
use App\Models\ReportRow;
use App\Models\UserGroup;
use App\Models\ReportCustom;
use Illuminate\Http\Request;

class UserGroupController extends Controller
{
  public function index()
  {
    $groups = UserGroup::with('reports')->get();
    $reports = Reports::all();
    return Inertia::render('GroupMaintenance', ['groups' => $groups, 'reports' => $reports]);
  }

  public function store(Request $request)
  {
    $group = $request->validate([
      'name' => 'required',
      'description' => 'required',
    ]);

    UserGroup::create($group);

    return redirect()->back()->with('success', 'Group created successfully!');
  }

  public function update(UserGroup $group, Request $request)
  {
    $validatdData = $request->validate([
      'name' => 'required',
      'description' => 'required',
    ]);

    $group->update($validatdData);

    return redirect()->back()->with('success', 'Group update successfully!');
  }

  public function addReports(UserGroup $group, Request $request)
  {
    // Check if removed report has an existing record on a user layout, if yes row will be removed
    $existingReport = $group->reports->pluck('id');
    $selectedReportId = collect($request->request)->where('check', true)->pluck('id')->toArray();
    $removeReport = [];
    foreach ($existingReport as $report) {
      if (!in_array($report, $selectedReportId)) {
        $removeReport[] = $report;
      }
    }
    if ($removeReport) {
      $reportsCustom = ReportCustom::whereIn('report_id', $removeReport)->groupBy('row_id')->pluck('row_id');
      $userId = $group->users->pluck('id')->toArray();
      ReportRow::whereIn('id', $reportsCustom)->whereIn('user_id', $userId)->delete();
    }
    // END

    // Delete existing reports on a group
    $group->reports()->detach();

    // Add updated group reports
    foreach ($request->request as $data) {
      if ($data['check']) {
        $group->reports()->attach($data['id']);
      }
    }

    return redirect()->back()->with('success', 'Group reports update successfully!');
  }
}
