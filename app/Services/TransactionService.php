<?php
// Copyright
declare(strict_types=1);


namespace App\Services;


use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TransactionService
{
    public function __construct(protected TransactionRepository $transactionRepository) { }

    public function listTransactions($request): LengthAwarePaginator
    {
        return $this->transactionRepository->listTransactions($request);
    }

    public function createNew($data)
    {
        $data['created_by'] = auth()->user()->id;
        $data['total_paid_amount'] = 0.0;
        $data['status'] = $this->calculateTransactionStatus($data['due_on'], 0, $data['amount']);

        return $this->transactionRepository->create($data);
    }

    public function getById($id)
    {
        return $this->transactionRepository->find($id, ['*'], ['payer', 'createdBy', 'payments']);
    }

    /**
     * @throws \Exception
     */
    public function update($id, $data)
    {
        $this->transactionRepository->update($id, $data) ;

        $transaction = $this->transactionRepository->find($id, ['*'], ['payer', 'createdBy', 'payments']);

        $this->updateStatus($transaction);

        return $transaction;
    }

    public function updateStatus($transaction): void
    {
        $totalPaidAmount = $transaction->payments()->sum('amount');

        $data['status'] = $this->calculateTransactionStatus(
             $transaction->due_on,
            $totalPaidAmount,
            $transaction->amount,
        );

        $transaction->update($data);
    }

    public function delete($id): bool
    {
        return $this->transactionRepository->deleteWithPayment($id);
    }

    private function calculateTransactionStatus($dueDate, $totalPaidAmount, $amount): string
    {
        $today = Carbon::now();

        $dueDate = Carbon::parse($dueDate);

        if ($dueDate < $today) {
            return 'overdue';
        } elseif ($dueDate == $today && $totalPaidAmount == 0) {
            return 'outstanding';
        } elseif ($totalPaidAmount >= $amount) {
            return 'paid';
        } elseif ($totalPaidAmount > 0 && $dueDate < $today) {
            return 'overdue';
        } else {
            return 'outstanding';
        }
    }
}
