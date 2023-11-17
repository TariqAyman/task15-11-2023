<?php
// Copyright
declare(strict_types=1);


namespace App\Services;

use App\Models\Payment;
use App\Repositories\PaymentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        protected PaymentRepository  $paymentRepository,
        protected TransactionService $transactionService
    )
    {
    }

    public function listPayments($request): LengthAwarePaginator
    {
        return $this->paymentRepository->listPayments($request);
    }

    public function createNew($data)
    {
        $data['created_by'] = auth()->user()->id;

        return $this->paymentRepository->create($data);
    }

    public function getById($id)
    {
        return $this->paymentRepository->find($id);
    }

    /**
     * @throws \Exception
     */
    public function updatePayment($id, $data)
    {
        $payment = $this->paymentRepository->update($id, $data);

        $this->transactionService->updateStatus($payment->transaction);

        return $payment;
    }

    public function delete($id): bool
    {
        return $this->paymentRepository->delete($id);
    }
}
