<?php

namespace App\Http\Controllers\DataTable;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\PrivateUserResource;

class UserController extends DataTableController
{
    protected $allowCreation = true;

    protected $allowDeletion = true;

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
            'id','name', 'username', 'email','created_at','updated_at'
        ];
    }
    public function getUpdatableColumns()
    {
        return [
           'name', 'username','email','created_at','updated_at'
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
        $this->builder->create(
            [
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                // 'password' =>$request->password,
                'password' => Hash::make($request->password),
                // 'created_at' => $request->created_at,
            ]
        );
        return "successfully created";
    }


    public function update($id, Request $request)
    {
        // return($id);
        // $request_object = get_object_vars($request);

        // return gettype($request_object);

        $this->validate($request, [
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|unique:users,email,' . $id . '|email',
            // 'created_at' => 'date'
        ]);

        $user = null;

        if($request->password_new){
           $this->builder->find($id)->update(
                [
                    
                    'name' => $request->name,
                    'username' => $request->username,
                    'email' => $request->email,
                    // 'password' =>$request->password,
                    'password' => Hash::make($request->password_new),

                ]);
            // $request->except(['password']);
            // $this->builder->find($id)->password = Hash::make($request->password)->save();
            // $user = User::find(1);
            // $user->timestamps = false;
            // $user->age = 72;
            // $user->save();
            return "password updated";
        } else {
            $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
        }

        // return new PrivateUserResource($user);
    }
}

