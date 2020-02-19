<?php


namespace App\Services;


use App\Models\Period;

class PeriodService
{

    public function store(string $name)
    {
        $period = new Period([
            'name' => $name
        ]);
        $period->saveOrFail();

        return $period;
    }

    public function update(int $id, string $name)
    {

        $period = Period::query()->findOrFail($id);
        $period->name = $name;
        $period->saveOrFail();

        return $period;
    }

    public function find(int $id)
    {
        $row = Period::query()->findOrFail($id);
        return $row;

    }

    public function destroy(int $id)
    {
        $row = Period::query()->findOrFail($id);
        $row->delete();
    }

    public function paginate($page = 1, $perPage = 15)
    {
        $data = Period::query()->paginate($perPage, ['*'], 'page', $page);
        return $data;
    }
}
