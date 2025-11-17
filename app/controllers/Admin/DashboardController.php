<?php
namespace App\Controllers\Admin;

use App\Core\AdminController;

class DashboardController extends AdminController
{
  public function index()
  {
    $this->view('layouts/admin_main', [
      'title' => 'Dashboard Admin',
      'content' => 'admin/dashboard/index',
      'page' => 'dashboard',
    ]);
  }
}
