<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;

class Director extends BaseModel
{
    public function getMetrics()
    {
        try {
            // Total Members
            $sql = "SELECT COUNT(*) as total FROM members WHERE status = 'Active'";
            $stmt = $this->getConnection()->query($sql);
            $totalMembers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Total Savings
            $sql = "SELECT SUM(current_amount) as total FROM savings_accounts WHERE status = 'active'";
            $stmt = $this->getConnection()->query($sql);
            $totalSavings = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Total Loans
            $sql = "SELECT 
                    COUNT(*) as total_loans,
                    SUM(amount) as total_amount,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_loans
                    FROM loans";
            $stmt = $this->getConnection()->query($sql);
            $loanStats = $stmt->fetch(PDO::FETCH_ASSOC);

            // New Members This Month
            $sql = "SELECT COUNT(*) as total FROM members 
                    WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
                    AND YEAR(created_at) = YEAR(CURRENT_DATE())";
            $stmt = $this->getConnection()->query($sql);
            $newMembers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return [
                'total_members' => $totalMembers,
                'total_savings' => $totalSavings,
                'loan_stats' => $loanStats,
                'new_members' => $newMembers
            ];

        } catch (\PDOException $e) {
            error_log('Database Error in getMetrics: ' . $e->getMessage());
            throw new \Exception('Error retrieving metrics');
        }
    }

    public function getRecentActivities($limit = 10)
    {
        try {
            $sql = "SELECT 
                    'savings' as type,
                    st.transaction_type,
                    st.amount,
                    st.created_at,
                    m.name as member_name
                FROM savings_transactions st
                JOIN savings_accounts sa ON st.savings_account_id = sa.id
                JOIN members m ON sa.member_id = m.id
                
                UNION ALL
                
                SELECT 
                    'loan' as type,
                    status as transaction_type,
                    amount,
                    created_at,
                    m.name as member_name
                FROM loans l
                JOIN members m ON l.member_id = m.id
                
                ORDER BY created_at DESC
                LIMIT :limit";

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error in getRecentActivities: ' . $e->getMessage());
            return [];
        }
    }

    public function getMembershipTrends()
    {
        try {
            $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as new_members
                FROM members
                WHERE created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month ASC";
            
            $stmt = $this->getConnection()->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error in getMembershipTrends: ' . $e->getMessage());
            return [];
        }
    }

    public function getFinancialMetrics()
    {
        try {
            // Get total savings with error handling
            try {
                $sql = "SELECT COALESCE(SUM(current_amount), 0) as total FROM savings_accounts WHERE status = 'active'";
                $stmt = $this->getConnection()->query($sql);
                $totalSavings = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            } catch (\PDOException $e) {
                error_log('Error getting savings: ' . $e->getMessage());
                $totalSavings = 0;
            }

            // Get total loans with error handling
            try {
                $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM loans WHERE status = 'approved'";
                $stmt = $this->getConnection()->query($sql);
                $totalLoans = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            } catch (\PDOException $e) {
                error_log('Error getting loans: ' . $e->getMessage());
                $totalLoans = 0;
            }

            // Get total fees with error handling
            try {
                $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM fees WHERE status = 'paid'";
                $stmt = $this->getConnection()->query($sql);
                $totalFees = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            } catch (\PDOException $e) {
                error_log('Error getting fees: ' . $e->getMessage());
                $totalFees = 0;
            }

            // Get other amounts with error handling
            try {
                $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM other_transactions WHERE status = 'completed'";
                $stmt = $this->getConnection()->query($sql);
                $otherAmounts = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            } catch (\PDOException $e) {
                error_log('Error getting other amounts: ' . $e->getMessage());
                $otherAmounts = 0;
            }

            return [
                'total_savings' => $totalSavings,
                'total_loans' => $totalLoans,
                'total_fees' => $totalFees,
                'other_amounts' => $otherAmounts
            ];

        } catch (\PDOException $e) {
            error_log('Database Error in getFinancialMetrics: ' . $e->getMessage());
            return [
                'total_savings' => 0,
                'total_loans' => 0,
                'total_fees' => 0,
                'other_amounts' => 0
            ];
        }
    }

    public function findByUsername($username)
    {
        try {
            $sql = "SELECT * FROM directors WHERE username = :username AND status = 'active'";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([':username' => $username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Database Error in findByUsername: ' . $e->getMessage());
            return null;
        }
    }

    public function updateLastLogin($id)
    {
        try {
            $sql = "UPDATE directors SET last_login = NOW() WHERE id = :id";
            $stmt = $this->getConnection()->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            error_log('Database Error in updateLastLogin: ' . $e->getMessage());
            return false;
        }
    }

    public function create($data)
    {
        try {
            $this->getConnection()->beginTransaction();

            $sql = "INSERT INTO directors (
                director_id,
                username,
                name,
                email,
                password,
                position,
                department,
                phone_number,
                status,
                created_at
            ) VALUES (
                :director_id,
                :username,
                :name,
                :email,
                :password,
                :position,
                :department,
                :phone_number,
                :status,
                NOW()
            )";

            $stmt = $this->getConnection()->prepare($sql);
            
            // Debug: Log prepared statement
            error_log('Prepared SQL: ' . $sql);

            $result = $stmt->execute([
                ':director_id' => $data['director_id'],
                ':username' => $data['username'],
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':password' => $data['password'],
                ':position' => $data['position'],
                ':department' => $data['department'],
                ':phone_number' => $data['phone_number'],
                ':status' => $data['status']
            ]);

            if (!$result) {
                // Debug: Log any SQL errors
                error_log('SQL Error: ' . print_r($stmt->errorInfo(), true));
                throw new \PDOException('Failed to execute SQL');
            }

            $this->getConnection()->commit();
            return true;

        } catch (\PDOException $e) {
            if ($this->getConnection()->inTransaction()) {
                $this->getConnection()->rollBack();
            }
            error_log('Database Error in create: ' . $e->getMessage());
            throw new \Exception('Failed to create director account: ' . $e->getMessage());
        }
    }

    public function generateDirectorId()
    {
        try {
            $year = date('Y');
            
            // Debug: Log the year
            error_log('Generating director ID for year: ' . $year);
            
            // Get the latest director number for the current year
            $sql = "SELECT director_id FROM directors 
                    WHERE director_id LIKE :year 
                    ORDER BY director_id DESC LIMIT 1";
            
            $stmt = $this->getConnection()->prepare($sql);
            $searchPattern = 'DIR' . $year . '%';
            $stmt->execute([':year' => $searchPattern]);
            
            // Debug: Log the search pattern
            error_log('Searching with pattern: ' . $searchPattern);
            
            $lastId = $stmt->fetchColumn();
            
            // Debug: Log the last ID found
            error_log('Last director ID found: ' . ($lastId ?: 'none'));
            
            if ($lastId) {
                // Extract the sequence number and increment
                $sequence = intval(substr($lastId, -4)) + 1;
            } else {
                // Start with 0001 if no existing directors for this year
                $sequence = 1;
            }
            
            // Format: DIRYYYY0001
            $newId = 'DIR' . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            
            // Debug: Log the generated ID
            error_log('Generated new director ID: ' . $newId);
            
            return $newId;
            
        } catch (\Exception $e) {
            error_log('Error generating director ID: ' . $e->getMessage());
            throw new \Exception('Failed to generate director ID: ' . $e->getMessage());
        }
    }

    public function getMembershipStats()
    {
        try {
            $sql = "SELECT 
                    SUM(CASE WHEN status = 'Active' THEN 1 ELSE 0 END) as active_count,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected_count
                    FROM members";
            
            $stmt = $this->getConnection()->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'memberCount' => (int)($result['active_count'] ?? 0),
                'pendingCount' => (int)($result['pending_count'] ?? 0),
                'rejectedCount' => (int)($result['rejected_count'] ?? 0)
            ];
        } catch (\PDOException $e) {
            error_log('Database Error in getMembershipStats: ' . $e->getMessage());
            return [
                'memberCount' => 0,
                'pendingCount' => 0,
                'rejectedCount' => 0
            ];
        }
    }

    public function updateLoanStatus($loanId, $status, $remarks)
    {
        try {
            $this->getConnection()->beginTransaction();

            $sql = "UPDATE loans 
                    SET status = :status,
                        reviewed_by = :director_id,
                        remarks = :remarks,
                        reviewed_at = NOW()
                    WHERE id = :loan_id";

            $stmt = $this->getConnection()->prepare($sql);
            $result = $stmt->execute([
                ':status' => $status,
                ':director_id' => $_SESSION['director_id'],
                ':remarks' => $remarks,
                ':loan_id' => $loanId
            ]);

            if ($result) {
                $this->getConnection()->commit();
                return true;
            }

            $this->getConnection()->rollBack();
            return false;

        } catch (\PDOException $e) {
            if ($this->getConnection()->inTransaction()) {
                $this->getConnection()->rollBack();
            }
            error_log('Database Error: ' . $e->getMessage());
            throw new \Exception('Gagal mengemaskini status pembiayaan');
        }
    }

    public function getPendingLoans()
    {
        try {
            // Debug connection
            error_log('Database connection status: ' . ($this->getConnection() ? 'Connected' : 'Not connected'));
            
            $sql = "SELECT l.*, m.name as member_name, m.ic_no 
                    FROM loans l
                    JOIN members m ON l.member_id = m.id
                    WHERE l.status = 'pending'
                    ORDER BY l.date_received DESC";
            
            error_log('Executing SQL: ' . $sql);
            
            // Try preparing and executing separately to catch specific errors
            $stmt = $this->getConnection()->prepare($sql);
            if (!$stmt) {
                error_log('Prepare failed: ' . print_r($this->getConnection()->errorInfo(), true));
                throw new \PDOException('Failed to prepare statement');
            }
            
            $success = $stmt->execute();
            if (!$success) {
                error_log('Execute failed: ' . print_r($stmt->errorInfo(), true));
                throw new \PDOException('Failed to execute statement');
            }
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log('Query results count: ' . count($results));
            
            return $results;

        } catch (\PDOException $e) {
            error_log('Database Error in getPendingLoans: ' . $e->getMessage());
            error_log('Error code: ' . $e->getCode());
            error_log('Error info: ' . print_r($e->errorInfo, true));
            throw new \Exception('Gagal mendapatkan senarai pembiayaan: ' . $e->getMessage());
        }
    }
} 