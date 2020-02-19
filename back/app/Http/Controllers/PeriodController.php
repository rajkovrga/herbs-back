<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePeriod;
use App\Http\Requests\UpdatePeriod;
use App\Mail\VerificateEmail;
use App\Services\PeriodService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PeriodController extends Controller
{
    private $periodService;

    public function __construct(PeriodService $periodService)
    {
        $this->periodService = $periodService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $periods = $this->periodService->paginate();
            return response()->json($periods);
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
    public function store(StorePeriod $request)
    {
        $data = $request->validated();

        if (!$request->user()->can('period'))
            abort(403);

        try {
            $period = $this->periodService->store($data['name']);
            return response()->json($period, 201);
        } catch (\DatabaseObjectExistsException $er) {
            return response()->json(['message' => 'Name exist'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $period = $this->periodService->find($id);
            return response()->json($period);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'This period not found'], 404);
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
    public function update(UpdatePeriod $request, $id)
    {
        $data = $request->validated();

        if (!$request->user()->can('period'))
            abort(403);

        try {
            $period = $this->periodService->update($id, $data['name']);
            return response()->json($period);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'This period not found'], 404);
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
    public function destroy(Request $request, $id)
    {
        if (!$request->user()->can('period'))
            abort(403);

        try {
            $this->periodService->destroy($id);
            return response()->json(['message' => 'Success deleted'], 200);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'This period not found'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }
}
