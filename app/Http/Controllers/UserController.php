<?php

namespace App\Http\Controllers;

use App\Events\GetUser;
use App\Models\User;
use App\Models\UserGroup;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\UserPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
  protected $decryptPwd;

  public function __construct(UserPassword $decryptPwd)
  {
    $this->decryptPwd = $decryptPwd;
  }

  public function index()
  {
    $users = User::with('group')->get();
    $groups = UserGroup::all();
    return Inertia::render('UserMaintenance', ['users' => $users, 'groups' => $groups]);
  }

  public function getIcssUser(Request $request)
  {
    $search = $request->search;
    $existingUser = User::all()->pluck('username')->toArray();
    $icssUsers = DB::connection('icss_db')->table('users')->where('usergrp_id', '!=', 'INACTIVE')->whereNotIn('user_id', $existingUser);
    // return $icssUsers->where('user_id', 'VROMERO')->get();
    if ($search) {
      $icssUsers->where('user_id', 'like', '%' . $search . '%')->orWhere('user_description', 'like', '%' . $search . '%')->whereNotIn('user_id', $existingUser);
    }
    return response()->json($icssUsers->orderBy('user_id', 'ASC')->paginate(10));
    // return Inertia::render('UserMaintenance', ['icssUsers' => $icssUsers]);
  }

  public function userPwdDecrypt(Request $request)
  {
    $userId = $request->user_id;
    $user = $this->decryptPwd->decryptUserPasswword($userId);

    // Create User to database
    $createdUser = User::create([
      'group_id' => null,
      'name' => $user->user_description,
      'password' => Hash::make($user->pwd),
      'username' => $user->user_id,
      'status' => True,
    ]);

    // session()->flash('success', 'User created successfully!');
    $createdUser->load('group');

    return response()->json(['user' => $createdUser, 'success' => 'User created successfully!']);
  }

  public function destroy(User $user)
  {
    $user->delete();
    $users = User::all();
    // GetUser::dispatch($users);
    return response()->json(['success' => 'User deleted successfully!']);
  }

  public function update(User $user, Request $request)
  {
    $validatedData = $request->validate([
      'status' => 'required',
      'group_id' => 'required',
    ]);

    $user->update($validatedData);

    return redirect()->back()->with('success', 'User update successfully!');
  }
  public function getUser()
  {
    $users = User::all();
    broadcast(new GetUser(['users' =>$users]));

  }
}
