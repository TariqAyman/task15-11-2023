<?php
/*
 * Created by PhpStorm.
 * Developer: Tariq Ayman ( tariq.ayman94@gmail.com )
 * Date: 4/14/22, 12:03 AM
 * Last Modified: 4/14/22, 12:03 AM
 * Project Name: GenCode
 * File Name: BaseRepository.php
 */

namespace App\Repositories\AbstractRepository;

use Carbon\Carbon;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection as Collect;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * Array of related models to eager load.
     *
     * @var array
     */
    private $with = [];

    /**
     * Array of one or more where in clause parameters.
     *
     * @var array
     */
    private $whereIns = [];

    /**
     * Array of one or more ORDER BY column/value pairs.
     *
     * @var array
     */
    private $orderBys = [];

    /**
     * Array of scope methods to call on the model.
     *
     * @var array
     */
    private $scopes = [];

    /**
     * Model instance related to current repository.
     *
     * @var Model
     */
    protected $model;

    /**
     * Attributes of the model.
     *
     * @var array
     */
    protected $attributes;

    /**
     * Unprocessed columns during insertion & updating.
     *
     * @var array
     */
    protected $excludedColumns = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Basic where clauses to affect query.
     *
     * @var array
     */
    protected $wheres = [];

    /**
     * The columns being selected.
     */
    protected $columns;

    /**
     * set limit of page
     *
     * @defalt 30
     *
     * @var int
     */
    protected $limit = 30;

    /**
     * set offset
     *
     * @var int
     */
    protected $offset = 0;

    /**
     * The query builder.
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;

    /**
     * Alias for the query limit.
     *
     * @var int
     */
    protected $take;

    /**
     * An array of relationships to eager load.
     *
     * @var array
     */
    protected $withs = [];

    /**
     * An array of relationships to count.
     *
     * @var array
     */
    protected $withCounts = [];

    /**
     * An array of where has queries
     *
     * @var array
     */
    protected $whereHas = [];

    /**
     * A nested array of relationships that must exist on a record.
     *
     * @var array
     */
    protected $has = [];

    /**
     * The column to order the selects by.
     *
     * @var string
     */
    protected $orderBy;

    /**
     * The direction to order selects in.
     *
     * @var string
     */
    protected $orderByDirection = 'ASC';

    /**
     * The relationships that should be eager loaded.
     *
     * @var array
     */
    public $relations = [];

    /**
     * All of the available clause operators.
     *
     * @var array
     */
    public $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'like binary', 'not like', 'between', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to', 'not ilike', '~~*', '!~~*',
    ];

    /**
     * Create a new repository instance.
     *
     * @throws Exception
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return Builder
     */
    public function getDatatable()
    {
        return $this->model::query()->latest();
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function findAll(array $columns = ['*'], array $relationships = []): Collection
    {
        return $this->model::query()->with($relationships)->select($columns)->get();
    }

    public function insert(array $data): bool
    {
        return $this->model::query()->insert($data);
    }

    public function findBy(array $data, array $columns = ['*']): Collection
    {
        $query = $this->model::query();
        foreach ($data as $column => $condition) {
            $query->where($column, '=', $condition);
        }

        return $query->select($columns)->get();
    }

    public function exists(string $key, $value, bool $withTrashed = false): bool
    {
        try {
            $query = $this->model::query()->where($key, $value);

            if ($withTrashed) {
                $query = $query->hasMacro('withTrashed') ? $query->withTrashed() : $query;
            }

            return $query->exists();
        } catch (QueryException $exc) {
            Log::error($exc->getMessage(), $exc->getTrace());

            return false;
        }
    }

    /**
     * @param mixed $attr_value
     * @return mixed|null
     *
     * @throws Exception
     */
    public function getByAttribute(
        string $attr_name,
               $attr_value,
        array  $relations = [],
        bool   $withTrashed = false,
        array  $selects = []
    )
    {
        try {

            $query = $this->initiateQuery($relations, $withTrashed, $selects);

            return $query->where($attr_name, $attr_value)->first();
        } catch (QueryException $exc) {
            Log::error($exc->getMessage(), $exc->getTrace());
            throw new Exception($exc->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public function getPaginate(int $n, array $relations = [], bool $withTrashed = false, array $selects = [])
    {
        $query = $this->initiateQuery($relations, $withTrashed, $selects);

        return $query->paginate($n);
    }

    /**
     * Create a new model record in the database.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function store(array $inputs)
    {
        try {
            return $this->getModel()::query()->create($inputs);
        } catch (QueryException $exc) {
            Log::error($exc->getMessage(), $exc->getTrace());
            throw new Exception($exc->getMessage());
        }
    }

    /**
     * Get the specified model record from the database.
     *
     *
     *
     * @throws Exception
     */
    public function getById($id, array $relations = [], bool $withTrashed = false, array $selects = []): mixed
    {
        try {
            $query = $this->initiateQuery($relations, $withTrashed, $selects);

            if (is_string($id)) {
                return $query->where('id', $id)->firstOrFail();
            }

            return $query->findOrFail($id);
        } catch (QueryException $exc) {
            Log::error($exc->getMessage(), $exc->getTrace());
            throw new Exception($exc->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public function search($key, $value, array $relations = [], bool $withTrashed = false, array $selects = [])
    {
        $query = $this->initiateQuery($relations, $withTrashed, $selects);

        return $query->where($key, 'like', '%' . $value . '%')->get();
    }

    /**
     * @return Builder[]|Collection
     */
    public function getAll(array $relations = [], bool $withTrashed = false, array $selects = [])
    {
        $query = $this->initiateQuery($relations, $withTrashed, $selects);

        return $query->get();
    }

    /**
     * @return mixed
     */
    public function countAll(bool $withTrashed = false)
    {
        $this->newQuery();

        $query = $this->query;

        if ($withTrashed) {
            $query = $query->hasMacro('withTrashed') ? $query->withTrashed() : $query;
        }

        return $query->count();
    }

    /**
     * @return mixed
     */
    public function getAllSelectable($key, string $attr = 'id')
    {
        $this->newQuery();

        return $this->query->pluck($key, $attr);
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function update($id, array $inputs)
    {
        try {
            $model = $this->getById($id);

            if (!$model) {
                return null;
            }

            $model->update($inputs);

            return $model->fresh();
        } catch (QueryException $exc) {
            Log::error($exc->getMessage(), $exc->getTrace());
            throw new Exception($exc->getMessage());
        }
    }

    /**
     * Delete record by ID
     *
     *
     *
     * @throws Exception
     */
    public function destroy($id): bool
    {
        try {
            $data = $this->getById($id);

            return $data ? $data->delete() : false;
        } catch (QueryException $exc) {
            Log::error($exc->getMessage(), $exc->getTrace());

            return false;
        }
    }

    /**
     * @throws \Exception
     */
    public function destroyAll(): bool
    {
        try {
            $this->newQuery();

            return $this->query->delete();
        } catch (QueryException $exc) {
            Log::error($exc->getMessage(), $exc->getTrace());

            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function forceDelete($id): bool
    {
        try {
            $data = $this->getById($id, [], true);

            return $data ? $data->forceDelete() : false;
        } catch (QueryException $exc) {
            Log::error($exc->getMessage(), $exc->getTrace());

            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function restore($id): bool
    {
        try {
            $data = $this->getById($id, [], true);

            return $data ? $data->restore() : false;
        } catch (QueryException $exc) {
            Log::error($exc->getMessage(), $exc->getTrace());

            return false;
        }
    }

    /**
     * @return Builder[]|Collection
     */
    public function getLatest(): Collection|array
    {
        $this->newQuery();

        return $this->query->latest()->get();
    }

    /**
     * @return mixed
     */
    public function getAllWithTranslation()
    {
        $this->newQuery();

        return $this->query->withTranslation()->latest()->get();
    }

    /**
     * Fetches the first row's data.
     * Execute a query for a single record by bindings.
     */
    public function firstByColumn($column, $value): ?Model
    {
        $this->newQuery();

        return $this->query->where($column, $value)->first();
    }

    /**
     * Get the first specified model record from the database.
     *
     * @param array $columns Column name
     * @return Model|static
     */
    public function first(array $columns = ['*'])
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        $model = $this->query->firstOrFail($columns);

        $this->unsetClauses();
        $this->unsetWith();

        return $model;
    }

    /**
     * Fetches a record based on the passed id.
     */
    public function find($id, array $columns = ['*'], array $relationships = []): ?Model
    {
        $this->newQuery()->eagerLoad();

        $query = $this->query->with($relationships);

        if (is_numeric($id)) {
            $query = $query->findOrFail($id, $columns);
        } else {
            $query = $query->where($this->model->getKeyName(), $id)->first($columns);
        }

        $this->unsetWith();

        return $query;
    }

    /**
     * Fetches records based on the passed column name & it's value.
     *
     * @param mixed $value
     * @param string $operator default is '='
     */
    public function findWhere(string $column, $value, string $operator = '='): Collection
    {
        return $this->model::query()->where($column, $operator, $value)->get();
    }

    /**
     * Fetches the first record based on the passed column name & it's value.
     *
     * @param mixed $value
     * @param string $operator default is '='
     */
    public function findFirstWhere(string $column, $value, string $operator = '='): ?Model
    {
        return $this->model::query()->where($column, $operator, $value)->first();
    }

    /**
     * Deletes a record of the given id.
     * if the record wasn't found, it will return **false**.
     */
    public function deleteOrFail(mixed $id, array $columns = ['*']): bool
    {
        $temp = $this->model::query()->findOrFail($id, $columns);

        if (is_null($temp)) {
            return false;
        }

        return $temp->delete();
    }

    /**
     * Deletes a record of the given id.
     * if the record wasn't found, it will return **false**.
     *
     * @param mixed $id
     */
    public function delete($id, array $columns = ['*']): bool
    {
        $this->newQuery();

        if (is_numeric($id)) {
            $model = $this->query->find($id, $columns);
        } else {
            $model = $this->query->where($this->model->getKeyName(), $id)->first();
        }

        if (is_null($model)) {
            return false;
        }

        return $model->delete();
    }

    /**
     * Returns the count of records.
     */
    public function countWhere(string $attribute, $value, string $operator = '='): int
    {
        $this->newQuery();

        return $this->query->where($attribute, $operator, $value)->count();
    }

    /**
     * Returns the sum of records.
     */
    public function sumWhere(string $attribute, $value, string $column, string $operator = '='): int
    {
        $this->newQuery();

        return $this->query->where($attribute, $operator, $value)->sum($column);
    }

    /**
     * Count the number of specified model records in the database.
     */
    public function count(): int
    {
        return $this->get()->count();
    }

    /**
     * @return mixed
     */
    public function browse(array $columns = ['*'])
    {
        $this->newQuery();

        return $this->query->get($columns);
    }

    /**
     * @return mixed
     */
    public function read($attribute, $value, array $columns = ['*'])
    {
        $this->newQuery();

        return $this->query->where($attribute, '=', $value)->first($columns);
    }

    /**
     * @return mixed
     */
    public function edit(array $data, $id, string $attribute = 'id')
    {
        $this->newQuery();

        return $this->query->where($attribute, '=', $id)->update($data);
    }

    /**
     * Paginate the given query into a simple paginator.
     * Fetches paginated records.
     *
     * @return mixed
     */
    public function paginate(int $perPage = 15, array $columns = ['*'])
    {
        $this->newQuery();

        return $this->query->paginate($perPage, $columns);
    }

    public function findByAttribute($attribute, $value, array $relationships = [])
    {
        $query = $this->initiateQuery($relationships);

        return $query->where($attribute, '=', $value)->first();
    }

    public function findByAttributeOrFail($attribute, $value, array $relationships = [])
    {
        $query = $this->initiateQuery($relationships);

        return $query->where($attribute, '=', $value)->firstOrFail();
    }

    /**
     * Add an basic where clause to the query.
     *
     * @param \Closure|string $column
     * @param mixed $value
     */
    public function where($column, $value = null, string $operator = null): Collection
    {
        $query = $this->initiateQuery();

        return $query->where($column, $operator, $value, 'or')->get();
    }

    /**
     * Add an "order by" clause to the query.
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public function orderBy(string $column, string $direction = 'asc', bool $withTrashed = false)
    {
        $query = $this->initiateQuery();

        $query = $query->orderBy($column, $direction);

        if ($withTrashed) {
            $query = $query->withTrashed();
        }

        return $query->get();
    }

    /**
     * Execute the query.
     */
    public function get(array $columns = ['*']): Collection
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        $models = $this->query->get($columns);

        $this->unsetWith();
        $this->unsetClauses();

        return $models;
    }

    /**
     * Create a new database record.
     *
     * @return mixed
     */
    public function create(array $attributes)
    {
        $this->newQuery();

        return $this->query->create($attributes);
    }

    /**
     * Upload a single file in the server
     * and return the random (string) filename if successful and (boolean) false if not
     *
     * @param null $folder
     * @return false|string
     */
    public function uploadOne(UploadedFile $file, $folder = null, string $disk = 'public')
    {
        return $file->store($folder, ['disk' => $disk]);
    }

    /**
     * @return mixed
     */
    public function getPaginatedModel(int $perPage = 25, string $orderBy = 'id', string $sortBy = 'asc')
    {
        $this->newQuery();

        return $this->query->orderBy($orderBy, $sortBy)->paginate($perPage);
    }

    /**
     * Delete multiple records.
     *
     * @param array $ids Ids
     */
    public function deleteMultipleById(array $ids): int
    {
        $this->newQuery();

        return $this->query->destroy($ids);
    }

    /**
     * Add a simple where in clause to the query
     *
     * @param mixed $values
     * @return \Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public function whereIn(string $key, $values)
    {
        $values = is_array($values) ? $values : [$values];

        $this->newQuery();

        return $this->query->whereIn($key, $values)->get();
    }

    /**
     * Get one or throw exception
     *
     * @param int|array $id ID
     * @param mixed $columns
     * @return mixed
     *
     */
    public function findOrFail($id, $columns = ['*'])
    {
        $this->newQuery()->eagerLoad();

        $type = $this->model->getKeyType();

        if ($type == 'int') {
            $model = $this->query->findOrFail($id, $columns);
        } else {
            $model = $this->query->where($this->model->getKeyName(), $id)->firstOrFail($columns);
        }

        $this->unsetWith();

        return $model;
    }

    /**
     * Find a resource by id
     */
    public function findOne(int $id): ?Model
    {
        $this->newQuery();

        return $this->query->where('id', $id)->first();
    }

    /**
     * Find a resource by criteria
     *
     * @return Model|null
     */
    public function findOneBy(array $data, array $columns = ['*']): Model
    {
        $this->newQuery();

        $query = $this->query;

        foreach ($data as $column => $condition) {
            $query->where($column, '=', $condition);
        }

        return $query->select($columns)->firstOrFail();
    }

    /**
     * Search All resources by any values of a key
     */
    public function findIn(string $key, array $values): Collection
    {
        $this->newQuery();

        return $this->query->whereIn($key, $values)->get();
    }

    /**
     * Returns a key pair value list from the model
     *
     * @return mixed
     */
    public function lists($idColumn, $valueColumn)
    {
        $this->newQuery();

        return $this->query->pluck($valueColumn, $idColumn);
    }

    /**
     * Make model
     *
     * @return Model|mixed
     *
     * @throws \Exception
     */
    public function makeModel(): mixed
    {
        $model = Container::getInstance()->make($this->model());

        if (!$model instanceof Model) {
            throw new Exception("Class {$this->model()} must be an instance of " . Model::class);
        }

        return $this->model = $model;
    }

    /**
     * Create one or more new model records in the database.
     *
     * @param array $data Data
     * @return \Illuminate\Database\Eloquent\Collection
     *
     * @throws Exception
     */
    public function createMultiple(array $data)
    {
        $models = new Collection();

        foreach ($data as $item) {
            $models->push($this->store($item));
        }

        return $models;
    }

    /**
     * Delete the specified model record from the database.
     *
     * @param int $id ID
     *
     * @throws Exception
     */
    public function deleteById($id): bool
    {
        return $this->getById($id)->delete();
    }

    /**
     * Set Eloquent relationships to eager load
     *
     * @param mixed $relations
     * @return $this
     */
    public function with($relations)
    {
        if (is_string($relations)) {
            $relations = explode(',', $relations);
        }

        if (!is_array($relations)) {
            $relations = func_get_args();
        }

        $this->with = $relations;

        return $this;
    }

    /**
     * Set the query limit
     *
     * @param int $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->take = $limit;

        return $this;
    }

    /**
     * Generates pagination of items in an array or collection.
     *
     * @param Collection|Collect $items Items
     * @param int $perPage Per page
     * @param int $page Number page
     * @return LengthAwarePaginator
     */
    public function generatesPaginate($items, $perPage = 10, $page = null)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collect::make($items);

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );
    }

    /**
     * Check to see if record exists
     *
     * @return bool|mixed
     */
    public function recordExists($id)
    {
        if (is_object($id)) {
            return true;
        }
        if (is_numeric($id) or $this->shouldBeNumber($id)) {
            return $this->findById($id, true);
        }
        if (is_string($id)) {
            return $this->findByIdentifier($id, true);
        }
    }

    /**
     * Find record by Id
     *
     * @param bool $existCheck
     * @return mixed
     */
    public function findById($id, $existCheck = false)
    {
        $record = $this->prepareQuery($this->model)->find($id);

        if ($existCheck) {
            return !empty($record);
        }

        return $record;
    }

    /**
     * Find by identifier
     *
     * @return mixed
     */
    public function findByIdentifier($uuid, $checkExists = false)
    {
        $columnName = $this->model->getUuidColumn() ?? 'identifier';

        $record = $this->prepareQuery($this->model->where($columnName, $uuid))->first();

        if ($checkExists) {
            return !empty($record);
        }

        return $record;
    }

    /**
     * Find by an array of credentials (return first)
     *
     * @return mixed
     */
    public function findByCredentialsFirst($array = [])
    {
        return $this->prepareQuery($this->createBuilder($array))->first();
    }

    /**
     * Find by an array of credentials (return all)
     *
     * @return mixed
     */
    public function findByCredentialsAll($array = [])
    {
        return $this->prepareQuery($this->createBuilder($array))->get();
    }

    /**
     * Find record
     *
     * @return mixed
     */
    public function findRecord($id)
    {
        if (is_object($id)) {
            return $id;
        }

        if (is_numeric($id) or $this->shouldBeNumber($id)) {
            return $this->findById($id);
        }

        if (is_string($id)) {
            return $this->findByIdentifier($id);
        }
    }

    /**
     * Update or Create record
     */
    public function updateOrCreate(array $attributes, array $values = []): mixed
    {
        $this->newQuery();

        return $this->query->updateOrCreate($attributes, $values);
    }

    /**
     * Get all items
     *
     * @param null $order
     * @return mixed
     */
    public function all($order = null)
    {
        $query = $this->createBuilder();

        if (!empty($order) and is_string($order)) {
            return $this->prepareQuery($query)->orderBy($order)->get();
        }

        if (!empty($order) and is_array($order)) {
            $build = $this->prepareQuery($query);

            foreach ($order as $orderItem) {
                $build = $build->orderBy($orderItem);
            }

            return $build->with($this->withs)->get();
        }

        return $this->prepareQuery($query)->get();
    }

    /**
     * Adds relations to count
     *
     * @return $this
     */
    public function withCount($withs)
    {
        if (!is_array($withs)) {
            $withs = func_get_args();
        }

        $this->withCounts = $withs;

        return $this;
    }

    /**
     * Adds in array of relationships that must exist on a record
     *
     * @param string $operator
     * @param int $value
     * @return $this
     */
    public function has($relation, $operator = '>=', $value = 1)
    {
        $this->has[] = compact('relation', 'operator', 'value');

        return $this;
    }

    /**
     * @param array $arguments
     * @return $this
     */
    public function addScope($scope, $arguments = [])
    {
        $this->scopes[$scope] = $arguments;

        return $this;
    }

    /**
     * Adds an array of whereHas queries
     *
     * @return $this
     */
    public function whereHas($relation)
    {
        if (!is_array($relation)) {
            $relation = func_get_args();
        }

        $this->whereHas[] = $relation;

        return $this;
    }

    /**
     * Determines if the ID should be a number.
     */
    public function shouldBeNumber($id): bool
    {
        $pattern = '/^[0-9]+?$/';

        if (preg_match($pattern, $id)) {
            return true;
        }

        return false;
    }

    /**
     * Create a new instance of the model's query builder.
     *
     * @return $this
     */
    public function newQuery()
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $this->query = $this->model->newQuery();

        return $this;
    }

    /**
     * Add relationships to the query builder to eager load.
     *
     * @return $this
     */
    public function eagerLoad()
    {
        foreach ($this->with as $relation) {
            $this->query->with($relation);
        }

        return $this;
    }

    /**
     * Set clauses on the query builder.
     *
     * @return $this
     */
    public function setClauses()
    {
        foreach ($this->wheres as $where) {
            $this->query->where(
                $where['column'],
                $where['operator'],
                $where['value']
            );
        }

        foreach ($this->whereIns as $whereIn) {
            $this->query->whereIn($whereIn['column'], $whereIn['values']);
        }

        foreach ($this->orderBys as $orders) {
            $this->query->orderBy($orders['column'], $orders['direction']);
        }

        if (isset($this->take) and !is_null($this->take)) {
            $this->query->take($this->take);
        }

        return $this;
    }

    /**
     * Set clauses scopes on the query builder.
     *
     * @param string $method
     * @param mixed $args
     * @return $this
     */
    public function scopes($method, ...$args)
    {
        $this->scopes[] = compact('method', 'args');

        return $this;
    }

    /**
     * Set query scopes.
     *
     * @return $this
     */
    public function setScopes()
    {
        foreach ($this->scopes as $scope) {
            if ($scope['args'] === []) {
                $this->query->{$scope['method']}();

                continue;
            }
            $args = '';
            foreach ($scope['args'] as $arg) {
                if (is_array($arg)) {
                    $args .= '[' . implode(', ', $arg) . '] ';

                    continue;
                }
                $args .= $arg . ' ';
            }
            $this->query->{$scope['method']}(trim($args));
        }

        return $this;
    }

    /**
     * Reset the query clause parameter arrays.
     *
     * @return $this
     */
    public function unsetClauses()
    {
        $this->wheres = [];
        $this->whereIns = [];
        $this->scopes = [];
        $this->take = null;
        $this->unsetOrderBy();

        return $this;
    }

    /**
     * Reset the query with arrays.
     *
     * @return $this
     */
    public function unsetWith()
    {
        if (!empty($this->with)) {
            $this->with = [];
        }

        return $this;
    }

    /**
     * Reset the query order by arrays.
     *
     * @return $this
     */
    public function unsetOrderBy()
    {
        if (!empty($this->orderBys)) {
            $this->orderBys = [];
        }

        return $this;
    }

    /**
     * Prepare the query for execution
     *
     * @return mixed
     */
    public function prepareQuery($query)
    {
        $query = $query->with($this->withs);

        if ($this->withCounts) {
            $query = $query->withCount($this->withCounts);
        }

        if ($this->orderBy) {
            $query = $query->orderBy($this->orderBy, $this->orderByDirection);
        }

        if (!empty($this->has)) {
            foreach ($this->has as $has) {
                $query = $query->has($has['relation'], $has['operator'], $has['value']);
            }
        }

        if (!empty($this->whereHas)) {
            foreach ($this->whereHas as $has) {
                $query = $query->whereHas($has[0], isset($has[1]) ? $has[1] : null);
            }
        }

        return $query;
    }

    /**
     * Create Builder
     *
     * @return Builder
     */
    public function createBuilder($array = [])
    {
        $builder = $this->model->newQuery();

        if (!empty($array)) {
            $builder->where($array);
        }

        if (!empty($this->scopes)) {
            foreach ($this->scopes as $scope => $args) {
                $builder->{$scope}($args);
            }
        }

        return $builder;
    }

    /**
     * @return Builder
     */
    public function initiateQuery(array $relations = [], bool $withTrashed = false, array $selects = [])
    {
        $this->unsetClauses();

        $this->newQuery();

        $query = $this->query;

        if (count($relations) > 0) {
            $query = $query->with($relations);
        }

        if (count($selects) > 0) {
            $query = $query->select($selects);
        }

        if ($withTrashed) {
            $query = $query->hasMacro('withTrashed') ? $query->withTrashed() : $query;
        }

        return $query;
    }

    /**
     * @return Builder|Model
     */
    public function firstOrCreate(array $attributes, array $values = [])
    {
        return $this->initiateQuery()->firstOrCreate($attributes, $values);
    }

    /**
     * Get query with Trashed
     */
    public function withTrashed(): Builder
    {
        $query = $this->initiateQuery();

        $query = $query->hasMacro('withTrashed') ? $query->withTrashed() : $query;

        return $query;
    }

    /**
     * Get count of model
     */
    public function countsPerMonth(array $filters): array
    {
        if (!$this->model()->getCreatedAtColumn()) {
            return [];
        }

        $from = data_get($filters, 'start_date') ?? Carbon::now()->subYear()->startOfMonth();
        $to = data_get($filters, 'end_date') ?? Carbon::now()->endOfMonth();

        $query = $this->model()::query()
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at');

        if (data_get($filters, 'start_date') and data_get($filters, 'end_date')) {
            $from = Carbon::parse($filters['start_date']);
            $to = Carbon::parse($filters['end_date']);

            $query->whereBetween('created_at', [$from, $to]);
        }

        if (isset($filters['store']) and $filters['store']) {
            $query->where('store_id', $filters['store']);
        }

        if (data_get($filters, 'status') and is_array($filters['status'])) {
            $query->whereIn('status', $filters['status']);
        }

        $result = $query->get(['created_at'])
            ->groupBy(function ($row) {
                return $row->created_at->format('Y_n');
            });

        $counts = [];

        while ($from->lt($to)) {
            $key = $from->format('Y_n');

            $counts[$this->parseMonthsDate($key)] = count($result->get($key, []));

            $from->addMonth();
        }

        return $counts;
    }

    /**
     * Get count of model
     */
    public function countsPerDay(array $filters): array
    {
        if (!$this->model()->getCreatedAtColumn()) {
            return [];
        }

        $from = data_get($filters, 'start_date') ?? Carbon::now()->subYear()->startOfMonth();
        $to = data_get($filters, 'end_date') ?? Carbon::now()->endOfMonth();

        $query = $this->model()::query()
            ->orderBy('created_at');

        if (data_get($filters, 'start_date') and data_get($filters, 'end_date')) {
            $from = Carbon::parse($filters['start_date']);
            $to = Carbon::parse($filters['end_date'])->addDay();
        }

        $query->whereBetween('created_at', [$from, $to]);

        if (isset($filters['store']) and $filters['store']) {
            $query->where('store_id', $filters['store']);
        }

        if (data_get($filters, 'status') and is_array($filters['status'])) {
            $query->whereIn('status', $filters['status']);
        }

        $result = $query->get(['created_at'])
            ->groupBy(function ($row) {
                return $row->created_at->format('Y/m/d');
            });


        $counts = [];

        while ($from->lt($to)) {
            $key = $from->format('Y/m/d');

            $counts[$key] = count($result->get($key, []));

            $from->addDay();
        }

        return $counts;
    }

    private function parseMonthsDate($yearMonth)
    {
        [$year, $month] = explode('_', $yearMonth);

        $month = trans("app.months.{$month}");

        return "{$month} {$year}";
    }

    private function parseDay($day)
    {
        [$dayName, $dayNumber] = explode('_', $day);

        $dayName = trans("app.weekDay.{$dayName}");

        return "{$dayNumber} {$dayName}";
    }
}
