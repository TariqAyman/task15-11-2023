<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Models\Transaction;
use App\Repositories\AbstractRepository\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TransactionRepository extends BaseRepository
{
    /**
     * @throws \Exception
     */
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }

    public function listTransactions($request): LengthAwarePaginator
    {
        $isUser = auth()->user()->isUser();

        $transaction = $this->model();

        if ($isUser) {
            $transaction = $transaction->where('payer_id', auth()->user()->id);
        }

        return $transaction->with(['payer', 'createdBy', 'payments'])
            ->paginate($request->get('perPage', 15))
            ->withQueryString()
            ->through(fn($transaction) => [
                'id' => $transaction->id,
                'payer' => $transaction->payer?->name ?? 'deleted',
                'created_by' => $transaction->createdBy?->name ?? 'deleted',
                'amount' => $transaction->amount,
                'due_on' => $transaction->due_on->format('d/m/Y'),
                'vat' => $transaction->vat,
                'is_vat_inclusive' => $transaction->is_vat_inclusive ? 'Yes' : 'No',
                'status' => $transaction->status,
                'total_paid_amount' => $transaction->total_paid_amount,
                'created_at' => $transaction->created_at->format('d/m/Y h:s a'),
            ]);
    }

    public function deleteWithPayment($id)
    {
        $transaction = $this->findOrFail($id);

        if ($transaction) {
            $transaction->payments()->delete();
            return $transaction->delete();
        }

        return false;
    }

    public function getReport(?string $start_date, ?string $end_date)
    {
        $report = $this->model()
            ->select(
                DB::raw('MONTH(due_on) as month'),
                DB::raw('YEAR(due_on) as year'),
                DB::raw('SUM(CASE WHEN status = "unpaid" THEN amount ELSE 0 END) as unpaid'),
                DB::raw('SUM(CASE WHEN status = "paid" THEN amount ELSE 0 END) as paid'),
                DB::raw('SUM(CASE WHEN status = "outstanding" THEN amount ELSE 0 END) as outstanding'),
                DB::raw('SUM(CASE WHEN status = "overdue" THEN amount ELSE 0 END) as overdue')
            );

        if ($start_date && $end_date) {
            $report = $report->whereBetween('due_on', [$start_date, $end_date]);
        }

        return $report->groupBy(DB::raw('MONTH(due_on), YEAR(due_on)'))->get();
    }
}
