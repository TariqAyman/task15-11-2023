<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\AbstractApiController;
use App\Http\Requests\Payment\PaymentCreateRequest;
use App\Http\Requests\Payment\PaymentUpdateRequest;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentsController extends AbstractApiController
{
    public function __construct(protected PaymentService $paymentService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $payments = $this->paymentService->listPayments($request);

        return $this->success($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentCreateRequest $request)
    {
        $payment = $this->paymentService->createNew($request->validationData());

        return $this->success($payment);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show(string $id)
    {
        $payment = $this->paymentService->getById($id);

        return $this->success($payment);
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(PaymentUpdateRequest $request, string $id)
    {
        $payment = $this->paymentService->updatePayment($id, $request->validationData());

        return $this->success($payment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->paymentService->delete($id);

        return $this->success('Successfully deleted!');
    }
}
