<?php

namespace App\Http\Controllers\DataTable;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

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
            'id', 'name', 'email', 'password','created_at'
        ];
    }
    public function getUpdatableColumns()
    {
        return [
           'name', 'email', 'password', 'created_at'
        ];
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'created_at' => 'date',
        ]);

        // return $request->password;

        // $request->password = Hash::make($request->password);

        // return $request;

        // $this->builder->create($request->only($this->getUpdatableColumns()));
        $this->builder->create(
            [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'created_at' => $request->created_at,
            ]
        );
    }


    public function update($id, Request $request)
    {
        // return($id);

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $id . '|email',
            'created_at' => 'date'
        ]);
           
        $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
    }
}

