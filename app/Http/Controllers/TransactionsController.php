<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\TransactionCreateRequest;
use App\Http\Requests\Transaction\TransactionUpdateRequest;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionsController extends Controller
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

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $edit = false;

        $users = $this->userService->listPluckUsers();

        return view('transactions.form', compact('edit', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionCreateRequest $request)
    {
        $transaction = $this->transactionService->createNew($request->validationData());

        return redirect()->route('transactions.edit', $transaction->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $edit = true;

        $users = $this->userService->list();

        $transaction = $this->transactionService->getById($id);

        return view('transactions.form', compact('edit', 'transaction', 'users'));
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(TransactionUpdateRequest $request, string $id)
    {
        $this->transactionService->update($id, $request->validationData());

        return redirect()->route('transactions.edit', $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->transactionService->delete($id);

        return redirect()->route('transactions.index');
    }
}
