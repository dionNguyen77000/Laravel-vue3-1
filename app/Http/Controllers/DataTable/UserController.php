<?php

namespace App\Http\Controllers\DataTable;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User\PrivateUserResource;

class UserController extends DataTableController
{
    protected $allowCreation = true;

    protected $allowDeletion = true;

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
          'roleOptions'=> $this->getRoleOptions(),
          'permissionOptions'=> $this->getPermissionOptions(),
          'allow' => [
              'creation' => $this->allowCreation,
              'deletion' => $this->allowDeletion,
          ]
        ]

      ]);
    }

    public function builder()
    {
        return User::query();
    }

    public function getCustomColumnsNames()
    {
        return [
            'name' => 'Full name',
            'email' => 'Email address',
        ];
    }

    public function getDisplayableColumns()
    {
        return [
            'id','name', 'username', 'email'
            // , 'created_at','updated_at'
        ];
    }
    public function getUpdatableColumns()
    {
        return [
           'name', 'username','email'
        //    ,'created_at','updated_at'
        ];
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|unique:users,email',
            'password' => 'required|confirmed',
        ]);

        // return $request->password;

        // $request->password = Hash::make($request->password);

        // return $request;

        // $this->builder->create($request->only($this->getUpdatableColumns()));
        $newUser =  $this->builder->create(
            [
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                // 'password' =>$request->password,
                'password' => Hash::make($request->password),
                // 'created_at' => $request->created_at,
            ]
        );

        
        if($request->assignedRoleIds && count($request->assignedRoleIds) > 0 ) {
            $newUser->roles()->attach($request->assignedRoleIds);
        }

        return $newUser;
    }


    public function update($id, Request $request)
    {
        // return($id);
        // $request_object = get_object_vars($request);

        // return gettype($request_object);
        // dd($request->assignedRoleIds);

        $this->validate($request, [
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|unique:users,email,' . $id . '|email',
            // 'created_at' => 'date'
        ]);

        $user = null;
        $theUser = $this->builder->find($id);
        if($request->password_new){
            $theUser->update(
                [
                    
                    'name' => $request->name,
                    'username' => $request->username,
                    'email' => $request->email,
                    // 'password' =>$request->password,
                    'password' => Hash::make($request->password_new),

                ]);
                $theUser->roles()->sync($request->assignedRoleIds);
            // $request->except(['password']);
            // $this->builder->find($id)->password = Hash::make($request->password)->save();
            // $user = User::find(1);
            // $user->timestamps = false;
            // $user->age = 72;
            // $user->save();
          
        } else {
            $theUser->update($request->only($this->getUpdatableColumns()));
            $theUser->roles()->sync($request->assignedRoleIds);
        }
        return $theUser->load('roles');
        // return new PrivateUserResource($user);
    }
    protected function getRecords(Request $request)
    {
        $builder = $this->builder;

        if ($this->hasSearchQuery($request)) {
            $builder = $this->buildSearch($builder, $request);
        }
     

        if (isset($request->supplier_id)) {
            $builder =   $builder->where('supplier_id','=',$request->supplier_id);
        }


        try {
            return PrivateUserResource::collection(
                $builder->limit($request->limit)
                ->orderBy('id', 'asc')
                ->get($this->getDisplayableColumns())
            );
            
          
        } catch (QueryException $e) {
            return [];
        }    
    }

    public function getRoleOptions()
    {
        $r = Role::all('id','name');

        $returnArr = [];
        foreach ($r as  $sr) {
            $returnArr[$sr['id']] = $sr['name'];
        }
        return $returnArr;
    }

    public function getPermissionOptions()
    {
        $r = Permission::all('id','name');

        $returnArr = [];
        foreach ($r as  $sr) {
            $returnArr[$sr['id']] = $sr['name'];
        }
        return $returnArr;
    }
}

