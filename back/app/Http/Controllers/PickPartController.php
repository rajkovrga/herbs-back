<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePickPart;
use App\Http\Requests\UpdatePickPart;
use App\Services\PickPartService;
use Doctrine\DBAL\Exception\DatabaseObjectExistsException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class PickPartController extends Controller
{

    private $partService;

    public function __construct(PickPartService $partService)
    {
        $this->partService = $partService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            $parts = $this->partService->paginate();
            return response()->json($parts);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePickPart $request)
    {
        $data = $request->validated();

        if (!$request->user()->can('pickpart'))
            abort(403);

        try {
            $part = $this->partService->store($data['name']);
            return response()->json($part);
        } catch (DatabaseObjectExistsException $er) {
            return response()->json(['message' => 'Name exist'], 404);
        } catch (Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        try {
            $part = $this->partService->find($id);
            return response()->json($part);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'This part not found'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePickPart $request, int $id)
    {
        $data = $request->validated();

        if (!$request->user()->can('pickpart'))
            abort(403);

        try {
            $part = $this->partService->update($data['name'], $id);
            return response()->json($part);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'This part not found'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,int $id)
    {
        if (!$request->user()->can('pickpart'))
            abort(403);

        try {
            $this->partService->destroy($id);
            return response()->json(['message' => 'Success deleted'], 200);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'This part not found'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }
}
