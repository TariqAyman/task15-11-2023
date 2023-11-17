<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\AbstractRepository\BaseRepository;

class UserRepository extends BaseRepository
{
    /**
     * @throws \Exception
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }


    public function list()
    {
        return $this->model()->paginate(15)->withQueryString();
    }

    public function listPluckUsers()
    {
        return $this->model()->user()->pluck('name', 'id');
    }
}
