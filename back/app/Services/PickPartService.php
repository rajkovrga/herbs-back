<?php


namespace App\Services;

use App\Models\Pickpart;

class PickPartService
{

    public function store(string $name)
    {
        $part = new Pickpart([
            'name' => $name
        ]);
        $part->saveOrFail();
        return $part;
    }

    public function update(string $name, int $id)
    {
        $part = Pickpart::query()->findOrFail($id);
        $part->name = $name;
        $part->saveOrFail();

        return $part;
    }

    public function find( int $id, \Closure $cl = null)
    {
        $row = Pickpart::query()->findOrFail($id);
        return $row;
    }

    public function destroy(int $id)
    {
        $row = Pickpart::query()->findOrFail($id);
        $row->delete();
    }



    public function paginate($page = 1, $perPage = 15)
    {
        $data = Pickpart::query()->paginate($perPage, ['*'], 'page', $page);
        return $data;
    }




}
