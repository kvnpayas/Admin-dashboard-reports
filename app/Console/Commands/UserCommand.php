<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Events\GetUser;
use Illuminate\Console\Command;

class UserCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:user-command';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Command description';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $users = User::all();
    broadcast(new GetUser($users));
  }
}
