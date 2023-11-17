<?php
/*
 * Created by PhpStorm.
 * Developer: Tariq Ayman ( tariq.ayman94@gmail.com )
 * Date: 4/14/22, 12:06 AM
 * Last Modified: 4/14/22, 12:03 AM
 * Project Name: GenCode
 * File Name: RepositoryInterface.php
 */

namespace App\Repositories\AbstractRepository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as Collect;

interface RepositoryInterface
{
    /**
     * @return Builder
     */
    public function getDatatable();

    public function findAll(array $columns = ['*'], array $relationships = []): Collect;

    public function insert(array $data): bool;

    public function findBy(array $data, array $columns = ['*']): Collect;

    public function exists(string $key, $value, bool $withTrashed = false): bool;

    /**
     * @param mixed $attr_value
     * @return mixed|null
     */
    public function getByAttribute(string $attr_name, $attr_value, array $relations = [], bool $withTrashed = false, array $selects = []);

    /**
     * @return mixed
     */
    public function getPaginate(int $n, array $relations = [], bool $withTrashed = false, array $selects = []);

    /**
     * Create a new model record in the database.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function store(array $inputs);

    /**
     * @return mixed
     */
    public function getById($id, array $relations = [], bool $withTrashed = false, array $selects = []);

    /**
     * @return mixed
     */
    public function search($key, $value, array $relations = [], bool $withTrashed = false, array $selects = []);

    /**
     * @return mixed
     */
    public function getAll(array $relations = [], bool $withTrashed = false, array $selects = []);

    /**
     * @return mixed
     */
    public function countAll(bool $withTrashed = false);

    /**
     * @return mixed
     */
    public function getAllSelectable($key, string $attr = 'id');

    /**
     * @return mixed
     */
    public function update($id, array $inputs);

    public function destroy($id): bool;

    /**
     * @throws \Exception
     */
    public function destroyAll(): bool;

    public function forceDelete($id): bool;

    public function restore($id): bool;

    /**
     * @return Builder[]|Collect
     */
    public function getLatest(): Collect|array;

    /**
     * @return mixed
     */
    public function getAllWithTranslation();

    /**
     * Fetches the first row's data.
     * Execute a query for a single record by bindings.
     */
    public function firstByColumn($column, $value): ?Model;

    /**
     * Get the first specified model record from the database.
     *
     * @param array $columns Column name
     * @return Model|static
     */
    public function first(array $columns = ['*']);

    /**
     * Fetches a record based on the passed id.
     */
    public function find(string $id, array $columns = ['*'], array $relationships = []): ?Model;

    /**
     * Fetches records based on the passed column name & it's value.
     *
     * @param mixed $value
     * @param string $operator default is '='
     */
    public function findWhere(string $column, $value, string $operator = '='): Collect;

    /**
     * Fetches the first record based on the passed column name & it's value.
     *
     * @param mixed $value
     * @param string $operator default is '='
     */
    public function findFirstWhere(string $column, $value, string $operator = '='): ?Model;

    /**
     * Deletes a record of the given id.
     * if the record wasn't found, it will return **false**.
     *
     * @param mixed $id
     */
    public function delete($id, array $columns = ['*']): bool;

    /**
     * Returns the count of records.
     */
    public function countWhere(string $attribute, $value, string $operator = '='): int;

    /**
     * Count the number of specified model records in the database.
     */
    public function count(): int;

    /**
     * @return mixed
     */
    public function browse(array $columns = ['*']);

    /**
     * @return mixed
     */
    public function read($attribute, $value, array $columns = ['*']);

    /**
     * @return mixed
     */
    public function edit(array $data, $id, string $attribute = 'id');

    /**
     * Paginate the given query into a simple paginator.
     * Fetches paginated records.
     *
     * @return mixed
     */
    public function paginate(int $perPage = 15, array $columns = ['*']);

    public function findByAttribute($attribute, $value, array $relationships = []);

    public function findByAttributeOrFail($attribute, $value, array $relationships = []);

    /**
     * Add an basic where clause to the query.
     *
     * @param \Closure|string $column
     * @param mixed $value
     */
    public function where($column, $value = null, string $operator = null): Collect;

    /**
     * Add an "order by" clause to the query.
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|Collect
     */
    public function orderBy(string $column, string $direction = 'asc', bool $withTrashed = false);

    /**
     * Execute the query.
     */
    public function get(array $columns = ['*']): Collect;

    /**
     * Create a new database record.
     *
     * @return mixed
     */
    public function create(array $attributes);

    /**
     * Upload a single file in the server
     * and return the random (string) filename if successful and (boolean) false if not
     *
     * @param null $folder
     * @return false|string
     */
    public function uploadOne(UploadedFile $file, $folder = null, string $disk = 'public');

    /**
     * @return mixed
     */
    public function getPaginatedModel(int $perPage = 25, string $orderBy = 'id', string $sortBy = 'asc');

    /**
     * Delete multiple records.
     *
     * @param array $ids Ids
     */
    public function deleteMultipleById(array $ids): int;

    /**
     * Add a simple where in clause to the query
     *
     * @param mixed $values
     * @return \Illuminate\Database\Eloquent\Builder[]|Collect
     */
    public function whereIn(string $key, array $values);

    /**
     * Get one or throw exception
     *
     * @param int|array $id ID
     * @param mixed $columns
     * @return mixed
     */
    public function findOrFail($id, $columns = ['*']);

    /**
     * Find a resource by id
     */
    public function findOne(int $id): ?Model;

    /**
     * Find a resource by criteria
     *
     * @return Model|null
     */
    public function findOneBy(array $data, array $columns = ['*']): Model;

    /**
     * Search All resources by any values of a key
     */
    public function findIn(string $key, array $values): Collect;

    /**
     * Returns a key pair value list from the model
     *
     * @return mixed
     */
    public function lists($idColumn, $valueColumn);

    /**
     * Make model
     *
     * @return Model|mixed
     *
     * @throws \Exception
     */
    public function makeModel();

    /**
     * Create one or more new model records in the database.
     *
     * @param array $data Data
     * @return \Illuminate\Database\Eloquent\Collection
     *
     * @throws Exception
     */
    public function createMultiple(array $data);

    /**
     * Delete the specified model record from the database.
     *
     * @param int $id ID
     * @return bool|null
     *
     * @throws \Exception
     */
    public function deleteById($id): bool;

    /**
     * Set Eloquent relationships to eager load
     *
     * @param mixed $relations
     * @return $this
     */
    public function with($relations);

    /**
     * Set the query limit
     *
     * @param int $limit
     * @return $this
     */
    public function limit($limit);

    /**
     * Generates pagination of items in an array or collection.
     *
     * @param Collect|Collect $items Items
     * @param int $perPage Per page
     * @param int $page Number page
     * @return LengthAwarePaginator
     */
    public function generatesPaginate($items, $perPage = 10, $page = null);

    /**
     * Check to see if record exists
     *
     * @return bool|mixed
     */
    public function recordExists($id);

    /**
     * Find record by Id
     *
     * @param bool $existCheck
     * @return mixed
     */
    public function findById($id, $existCheck = false);

    /**
     * Find by identifier
     *
     * @return mixed
     */
    public function findByIdentifier($uuid, $checkExists = false);

    /**
     * Find by an array of credentials (return first)
     *
     * @return mixed
     */
    public function findByCredentialsFirst($array = []);

    /**
     * Find by an array of credentials (return all)
     *
     * @return mixed
     */
    public function findByCredentialsAll($array = []);

    /**
     * Find record
     *
     * @return mixed
     */
    public function findRecord($id);

    /**
     * Update or Create record
     *
     * @return mixed
     */
    public function updateOrCreate(array $attributes, array $values = []);

    /**
     * Get all items
     *
     * @param null $order
     * @return mixed
     */
    public function all($order = null);

    /**
     * Adds relations to count
     *
     * @return $this
     */
    public function withCount($withs);

    /**
     * Adds in array of relationships that must exist on a record
     *
     * @param string $operator
     * @param int $value
     * @return $this
     */
    public function has($relation, $operator = '>=', $value = 1);

    /**
     * @param array $arguments
     * @return $this
     */
    public function addScope($scope, $arguments = []);

    /**
     * Adds an array of whereHas queries
     *
     * @return $this
     */
    public function whereHas($relation);

    /**
     * Determines if the ID should be a number.
     */
    public function shouldBeNumber($id): bool;

    /**
     * Create a new instance of the model's query builder.
     *
     * @return $this
     */
    public function newQuery();

    /**
     * Add relationships to the query builder to eager load.
     *
     * @return $this
     */
    public function eagerLoad();

    /**
     * Set clauses on the query builder.
     *
     * @return $this
     */
    public function setClauses();

    /**
     * Set clauses scopes on the query builder.
     *
     * @param string $method
     * @param mixed $args
     * @return $this
     */
    public function scopes($method, ...$args);

    /**
     * Set query scopes.
     *
     * @return $this
     */
    public function setScopes();

    /**
     * Reset the query clause parameter arrays.
     *
     * @return $this
     */
    public function unsetClauses();

    /**
     * Reset the query with arrays.
     *
     * @return $this
     */
    public function unsetWith();

    /**
     * Reset the query order by arrays.
     *
     * @return $this
     */
    public function unsetOrderBy();

    /**
     * Prepare the query for execution
     *
     * @return mixed
     */
    public function prepareQuery($query);

    /**
     * Create Builder
     *
     * @return Builder
     */
    public function createBuilder($array = []);

    /**
     * @return Builder
     */
    public function initiateQuery(array $relations = [], bool $withTrashed = false, array $selects = []);
}
