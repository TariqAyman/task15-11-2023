<?php
// Copyright
declare(strict_types=1);


namespace App\Services;

use App\Repositories\PaymentRepository;
use App\Repositories\UserRepository;

class UserService
{
    public function __construct(protected UserRepository $userRepository)
    {
    }

    public function list()
    {
        return $this->userRepository->list();
    }

    public function listPluckUsers()
    {
        return $this->userRepository->listPluckUsers();
    }

    public function create($data)
    {
        return $this->userRepository->create($data);
    }

    public function getById($id)
    {
        return $this->userRepository->find($id, ['*'], ['transactions', 'createdPayment', 'createdTransaction']);
    }

    /**
     * @throws \Exception
     */
    public function update($id, $data)
    {
        return $this->userRepository->update($id, $data);
    }

    public function delete($id): bool
    {
        return $this->userRepository->delete($id);
    }
}
