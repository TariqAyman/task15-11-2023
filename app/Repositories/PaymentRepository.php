<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Repositories\AbstractRepository\BaseRepository;
use Illuminate\Support\Str;

class PaymentRepository extends BaseRepository
{
    /**
     * @throws \Exception
     */
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    public function listPayments($request)
    {
        return $this->model::query()
            ->paginate($request->get('perPage', 15))
            ->withQueryString()
            ->through(fn($payment) => [
                'id' => $payment->id,
                'created_by' => $payment->createdBy?->name ?? 'deleted',
                'amount' => $payment->amount,
                'paid_on' => $payment->paid_on->format('d/m/Y h:s a'),
                'details' => $payment->details,
                'created_at' => $payment->created_at->format('d/m/Y h:s a'),
                'transaction_id' => $payment->transaction_id,
            ]);
    }
}
