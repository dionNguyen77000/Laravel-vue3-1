<?php

namespace App\Http\Controllers\DataTable;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Arr;

abstract class DataTableController extends Controller
{ /**
    * If an entity is allowed to be created.
    *
    * @var boolean
    */
   protected $allowCreation = true;

   /**
    * Allow deletion.
    *
    * @var boolean
    */
   protected $allowDeletion = true;

   /**
    * The entity builder.
    *
    * @var Illuminate\Database\Eloquent\Builder
    */

   protected $builder;


    abstract public function builder();


    
    /**
     * Create the controller, check builder method and assign
     * to the builder property.
     *
     * @return void
     */


    public function __construct()
    {
        $builder =$this->builder();
        if(!$builder instanceof Builder) {
            throw new Exception('Entity buider not instance of Builder');
        }

        $this->builder = $builder;
    }


      /**
     * Show a list of entities.
     *
     * @return Illuminate\Http\JsonResponse
     */


    public function index(Request $request)
    {
    //   return $this->builder->get();
      return response()->json([
        'data' => [
          'table' => $this->builder->getModel()->getTable(),
          'db_column_name' =>array_values($this->getDatabaseColumnNames()),
          'displayable' => array_values($this->getDisplayableColumns()),
          'updatable' => array_values($this->getUpdatableColumns()),
          'records' => $this->getRecords($request),
          'custom_columns' => $this->getCustomColumnsNames(),
          'allow' => [
              'creation' => $this->allowCreation,
              'deletion' => $this->allowDeletion,
          ]
        ]

      ]);
    }

    
    /**
     * Create an entity.
     *
     * @param  Request $request
     * @return Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$this->allowCreation) {
            return;
        }

        $this->builder->create($request->only($this->getUpdatableColumns()));
    }

       /**
     * Update an entity.
     *
     * @param  integer  $id
     * @param  Request $request
     *
     * @return Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
    }

        /**
     * Delete an entity.
     *
     * @param  integer  $id
     * @param  Request $request
     *
     * @return Illuminate\Http\Response
     */

     // delete single id
    // public function destroy($id, Request $request)
    // {
    //     if (!$this->allowDeletion) {
    //         return;
    //     }

    //     $this->builder->find($id)->delete();
    // }

    public function destroy($ids, Request $request)
    {
        if (!$this->allowDeletion) {
            return;
        }

        $arrayIds = explode(',',$ids);

        if (count($arrayIds) > 1 ) {
            $this->builder->whereIn('id', explode(',', $ids))->delete();
        } else if (count($arrayIds) == 1){
            $this->builder->find($ids)->delete();
            // $this->builder->find($ids)->delete();
        }
    }


  /**
     * Get the columns that are allowed to be displayed.
     *
     * @return array
     */
    public function getDisplayableColumns()
    {
        return array_diff(
            $this->getDatabaseColumnNames(), $this->builder->getModel()->getHidden()
        );
    }

    /**
     * Get the columns that are allowed to be updated.
     *
     * @return array
     */
    public function getUpdatableColumns()
    {
        return array_intersect($this->getDatabaseColumnNames(), $this->getDisplayableColumns());
    }

     
    /** 
     * get Custom column names
     * @return  array
     */
    public function getCustomColumnsNames()
    {
        return [];
    }

    
    /**
     * Get the database column names for the entity.
     *
     * @return array
     */

    protected function getDatabaseColumnNames()
    {
        return Schema::getColumnListing($this->builder->getModel()->getTable());
        
        // $this->builder->getModel()->getTable() : this return table
        // Schema::getColumnListing: return column name
    }

    protected function getRecords(Request $request)
    {
            // return $this->builder->get();
            $builder = $this->builder;

        if ($this->hasSearchQuery($request)) {
            $builder = $this->buildSearch($builder, $request);
        }

        try {
            return $builder->limit($request->limit)->orderBy('id', 'asc')->get($this->getDisplayableColumns());
        } catch (QueryException $e) {
            return [];
        }    
    }

       /**
     * If the request has the columns required to search.
     *
     * @param  Request $request
     * @return boolean
     */
    protected function hasSearchQuery(Request $request)
    {
        return count(array_filter($request->only(['column', 'operator', 'value']))) === 3;
    }
    protected function hasSearchQuery_1(Request $request)
    {
        return count(array_filter($request->only(['column_1', 'operator_1', 'value_1']))) === 3;
    }

    /**
     * Resolve the given operator to perform a query.
     *
     * @param  string $operator
     * @return string
     */
    protected function resolveQueryParts($operator, $value)
    {
        return Arr::get([
            'equals' => [
                'operator' => '=',
                'value' => $value
            ],
            'contains' => [
                'operator' => 'LIKE',
                'value' => "%{$value}%"
            ],
            'starts_with' => [
                'operator' => 'LIKE',
                'value' => "{$value}%"
            ],
            'ends_with' => [
                'operator' => 'LIKE',
                'value' => "%{$value}"
            ],
            'greater_than' => [
                'operator' => '>',
                'value' => $value
            ],
            'less_than' => [
                'operator' => '<',
                'value' => $value
            ],
            'greater_than_or_equal_to' => [
                'operator' => '>=',
                'value' => $value
            ],
            'less_than_or_equal_to' => [
                'operator' => '<=',
                'value' => $value
            ],
        ], $operator);
    }

    /**
     * Build the search.
     *
     * @param  Builder $builder
     * @param  Request $request
     *
     * @return Builder
     */
    protected function buildSearch(Builder $builder, Request $request)
    {
        $queryParts = $this->resolveQueryParts($request->operator, $request->value);

        return $builder->where(
            $request->column,
            $queryParts['operator'],
            $queryParts['value']
        );
    }
    protected function buildSearch_1(Builder $builder, Request $request)
    {
        $queryParts = $this->resolveQueryParts($request->operator_1, $request->value_1);

        return $builder->where(
            $request->column_1,
            $queryParts['operator'],
            $queryParts['value']
        );
    }
    
}
