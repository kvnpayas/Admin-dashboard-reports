<?php

namespace App\Http\Controllers;

use App\Models\CustomerStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\CustomerStatusUpdated;

class CustomerStatusController extends Controller
{
  public function getCustomerStatus(Request $request)
  {
    if ($request->date) {
      $fetchData = DB::connection('icss_db')->select("execute dbo.sp_custservice_customer_status;1 @RunDate = '" .$request->date."'");
      $accounts = collect($fetchData)->whereIn('tag', ['Active Accounts', 'Connected', 'Disconnected'])->toArray();
      $accountsParam = collect($fetchData)->whereNotIn('tag', ['Active Accounts', 'Connected', 'Disconnected'])->toArray();
    } else {
      $fetchData = CustomerStatus::all();

      $accounts = $fetchData->whereIn('tag', ['Active Accounts', 'Connected', 'Disconnected'])->toArray();
      $accountsParam = $fetchData->whereNotIn('tag', ['Active Accounts', 'Connected', 'Disconnected'])->toArray();

    }
    return response()->json(['accounts' => array_values($accounts), 'accountsParam' => array_values($accountsParam)]);
  }
}
