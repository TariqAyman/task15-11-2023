<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\PaymentCreateRequest;
use App\Http\Requests\Payment\PaymentUpdateRequest;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentsController extends Controller
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

        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $edit = false;

        return view('payments.form', compact('edit'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentCreateRequest $request)
    {
        $payment = $this->paymentService->createNew($request->validationData());

        return redirect()->route('payments.edit', $payment->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $edit = true;

        $payment = $this->paymentService->getById($id);

        return view('payments.form', compact('edit', 'payment'));
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(PaymentUpdateRequest $request, string $id)
    {
        $this->paymentService->updatePayment($id, $request->validationData());

        return redirect()->route('payments.edit', $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->paymentService->delete($id);

        return redirect()->route('payments.index');
    }
}
