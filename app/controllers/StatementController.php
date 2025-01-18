<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Statement;
use App\Models\Saving;

class StatementController extends BaseController
{
    private $statement;
    private $saving;

    public function __construct()
    {
        $this->statement = new Statement();
        $this->saving = new Saving();
    }

    public function index()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $memberId = $_SESSION['member_id'];
            
            // Get member's savings account first
            $account = $this->saving->getSavingsAccount($memberId);
            if (!$account) {
                throw new \Exception('Akaun simpanan tidak dijumpai');
            }

            $statements = $this->statement->getStatementsByMemberId($memberId);

            $this->view('users/statement/index', [
                'statements' => $statements,
                'account' => $account
            ]);

        } catch (\Exception $e) {
            error_log('Error in statement index: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/dashboard');
            exit;
        }
    }

    public function generate()
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $memberId = $_SESSION['member_id'];
            $account = $this->saving->getSavingsAccount($memberId);

            if (!$account) {
                throw new \Exception('Akaun simpanan tidak dijumpai');
            }

            $statementId = $this->statement->generateStatement($memberId, $account['id']);
            
            header('Location: /users/statements/download/' . $statementId);
            exit;

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/statements');
            exit;
        }
    }

    public function download($id)
    {
        try {
            if (!isset($_SESSION['member_id'])) {
                throw new \Exception('Sila log masuk untuk mengakses');
            }

            $statement = $this->statement->getStatementById($id);
            
            if (!$statement || $statement['member_id'] != $_SESSION['member_id']) {
                throw new \Exception('Penyata tidak dijumpai');
            }

            $this->view('users/statement/download', [
                'statement' => $statement
            ]);

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /users/statements');
            exit;
        }
    }
}