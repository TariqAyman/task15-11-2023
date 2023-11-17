<?php
// Copyright
declare(strict_types=1);


namespace App\Services;

use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function __construct(protected TransactionRepository $transactionRepository)
    {

    }

    public function getReport(?string $start_date, ?string $end_date)
    {
        return $this->transactionRepository->getReport($start_date, $end_date);
    }
}
