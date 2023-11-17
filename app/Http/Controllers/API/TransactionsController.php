<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Transaction\TransactionCreateRequest;
use App\Http\Requests\Transaction\TransactionUpdateRequest;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionsController extends AbstractApiController
{
    public function __construct(
        protected TransactionService $transactionService,
        protected UserService        $userService
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $transactions = $this->transactionService->listTransactions($request);

        return $this->success($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionCreateRequest $request)
    {
        $transaction = $this->transactionService->createNew($request->validationData());

        return $this->success($transaction);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show(string $id)
    {
        $users = $this->userService->listPluckUsers();

        $transaction = $this->transactionService->getById($id);

        return $this->success(['transaction' => $transaction, 'users' => $users]);
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(TransactionUpdateRequest $request, string $id)
    {
        $transaction = $this->transactionService->update($id, $request->validationData());

        return $this->success($transaction);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->transactionService->delete($id);

        return $this->success('Successfully deleted!');
    }
}
