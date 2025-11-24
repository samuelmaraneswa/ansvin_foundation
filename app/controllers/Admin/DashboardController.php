<?php
namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Config;

class DashboardController extends AdminController
{
  public function index()
  {
    $this->view('layouts/admin_main', [
      'title' => 'Dashboard Admin',
      'content' => 'admin/dashboard/index',
      'page' => 'dashboard',
      'base_url' => Config::get('base_url')
    ]);
  }
}
