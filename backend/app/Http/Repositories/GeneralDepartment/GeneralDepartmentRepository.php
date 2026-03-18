<?php

namespace App\Http\Repositories\GeneralDepartment;

use App\Models\General_Dep;

class GeneralDepartmentRepository
{
    public function getAll()
    {
        return General_Dep::with('store')->get();
    }

    public function find($id)
    {
        return General_Dep::with('store')->find($id);
    }

    public function create(array $data)
    {
        return General_Dep::create($data);
    }

    public function update(General_Dep $generalDep, array $data)
    {
        $generalDep->update($data);
        return $generalDep;
    }

    public function delete(General_Dep $generalDep)
    {
        $generalDep->delete();
    }
}
