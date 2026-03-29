<?php

namespace App\Http\Repositories\Department;

use App\Models\Department;

class DepartmentRepository
{
    public function getAll()
    {
        return Department::with('generalDep')->get();
    }

    public function find($id)
    {
        return Department::with('generalDep')->find($id);
    }

    public function create(array $data)
    {
        return Department::create($data);
    }

    public function update(Department $department, array $data)
    {
        $department->update($data);
        return $department;
    }

    public function delete(Department $department)
    {
        $department->delete();
    }
}
