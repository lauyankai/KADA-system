<?php
namespace App\Controllers;

use App\Core\BaseController;

class InfoController extends BaseController
{
    public function showLoanTypes()
    {
        $this->view('info/loantype');
    }
} 