<?php

use App\Http\Controllers\GetLatestController;
use App\Http\Controllers\UserGroupController;
use Inertia\Inertia;
use App\Models\ReportRow;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\MeterreadingController;
use App\Http\Controllers\ReportCustomController;
use App\Http\Controllers\CustomerStatusController;



// Authentication
Route::middleware(['guest'])->group(function () {
  Route::get('/login', function () {
    return Inertia::render('Auth/Login');
  });
  Route::post('/login', [AuthController::class, 'login']);
});
Route::middleware(['auth'])->group(function () {
  Route::get('/', function () {
    $customReport = ReportRow::where('user_id', Auth::user()->id)->with('reportCols')->get();
    foreach ($customReport as $custom) {
      $custom->reportCols->map(function ($item) {
        $item->name = $item->report->name;
        $item->component = $item->report->component;
        $item->max_col = $item->report->max_col; 
        return $item;
      });
    }
    return Inertia::render('Dashboard', ['customReport' => $customReport]);
  });

  // Report Lists
  Route::get('/report-lists', [ReportCustomController::class, 'getReports']);

  Route::get('/user-maintenance', [UserController::class, 'index'])->middleware('admin');
  Route::get('/user-maintenance/icss-users', [UserController::class, 'getIcssUser'])->middleware('admin');
  Route::patch('/user-maintenance/{user}', [UserController::class, 'update'])->middleware('admin');
  Route::post('/user-maintenance/decrypt-password/{user_id}', [UserController::class, 'userPwdDecrypt'])->middleware('admin');
  Route::delete('/user-maintenance/delete/{user}', [UserController::class, 'destroy'])->middleware('admin');

  Route::get('/group-maintenance', [UserGroupController::class, 'index'])->middleware('admin');
  Route::post('/group-maintenance', [UserGroupController::class, 'store'])->middleware('admin');
  Route::patch('/group-maintenance/{group}', [UserGroupController::class, 'update'])->middleware('admin');
  Route::post('/group-maintenance/{group}', [UserGroupController::class, 'addReports'])->middleware('admin');

  Route::get('/dashboard-customizer', [ReportCustomController::class, 'index']);
  Route::post('/dashboard-customizer', [ReportCustomController::class, 'save']);

  Route::post( '/logout', [AuthController::class, 'logout']);

  // DATA
  Route::post('/customer-status', [CustomerStatusController::class, 'getCustomerStatus']);
  Route::post('/meter-reading', [MeterreadingController::class, 'getMeterReading']);
  Route::post('/collection-location', [CollectionController::class, 'getCollectionReportLocation']);
  Route::post('/collection-summary', [CollectionController::class, 'getCollectionSummaryCross']);
  Route::post('/collection-summary-payment', [CollectionController::class, 'getCollectionSummaryPayment']);
  // Route::post('/customer-status', action: [CustomerStatusController::class, 'getCustomerStatusWithParams']);
  // END DATA

  Route::post('/get-latest/{report}', [GetLatestController::class, 'updateLiveStatus']);
});
