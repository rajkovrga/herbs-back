<?php

namespace App\Http\Controllers;

use App\DTO\HerbDTO;
use App\DTO\HerbFilterDTO;
use App\Http\Requests\FilterHerbs;
use App\Http\Requests\ImageRequest;
use App\Http\Requests\StoreHerb;
use App\Http\Requests\UpdateHerb;
use App\Models\Herb;
use App\Services\HerbsService;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HerbsController extends Controller
{

    private $herbsService;
    private $imageService;

    public function __construct(HerbsService $herbsService, ImageService $imageService)
    {
        $this->herbsService = $herbsService;
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return array
     */

    public function index(int $page)
    {
        try {
            $herbs = $this->herbsService->paginate($page, 9);
            return response()->json($herbs);
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
    public function store(StoreHerb $request, $urlImage = null)
    {
        $data = $request->validated();

        if (!$request->user()->can('add-herbs'))
            abort(403);

        try {
            $urlImage = $this->imageService->uploadImage(Herb::class, $request->file('file'), 'HERBS');
            $herbs = $this->herbsService->store(new HerbDTO([
                'srb_name' => $data['srb_name'],
                'lat_name' => $data['lat_name'],
                'toxic' => $data['toxic'],
                'endangered' => $data['endangered'],
                'pickpart_id' => $data['pickpart_id'],
                'period_id' => $data['period_id'],
                'desc' => $data['desc'],
                'url_image' => $urlImage
            ]));

            return response()->json($herbs, 201);
        } catch (\PDOException $er) {
            ($urlImage != null) ? Storage::disk('local')->delete($urlImage) : null;
            return response()->json(['message' => 'Exist name'], 404);
        } catch (\Exception $er) {
            ($urlImage != null) ? Storage::disk('local')->delete($urlImage) : null;
            return response()->json(['message' => 'Error'], 500);
        }


    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return array
     */
    public function comments(int $id, int $page)
    {
        try {
            $comments = $this->herbsService->comments($id, $page);
            return response()->json($comments);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'Problem with comments for this herb'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return array
     **/
    public function show(int $id)
    {
        try {
            $herb = $this->herbsService->find($id);
            return response()->json($herb);
        } catch (ModelNotFoundException $er) {
            response()->json(['message' => 'Herb not found'], 404);
        } catch (Exception $er) {
            response()->json(['message' => 'Error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $id)
    {
        if (!$request->user()->can('delete-herbs'))
            abort(403);

        try {
            $img = $this->herbsService->destroy($id);
            Storage::disk('local')->delete($img);
            return response()->json(['message' => 'Herb deleted'], 200);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'This herb not found'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function imageChange(ImageRequest $request, int $id)
    {
        $data = $request->validated();

        if (!$request->user()->can('update-herbs'))
            abort(403);

        try {
            $this->imageService->changeImagePhoto(Herb::class, $request->file('file'), $id, 'HERBS');
            return response()->json(['message' => 'Image changed'], 200);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'This herb not found'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function update(UpdateHerb $request, int $id)
    {
        $data = $request->validated();

        if (!$request->user()->can('update-herbs'))
            abort(403);

        try {
            $herb = $this->herbsService->update(new HerbDTO([
                'srb_name' => $data['srb_name'],
                'lat_name' => $data['lat_name'],
                'toxic' => $data['toxic'],
                'endangered' => $data['endangered'],
                'pickpart_id' => $data['pickpart_id'],
                'period_id' => $data['period_id'],
                'desc' => $data['desc']
            ]), $id);

            return response()->json($herb);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'This herb not found'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }

    }

    public function filter(FilterHerbs $request, $page)
    {
        $data = $request->validated();

        try {
            $herbs = $this->herbsService->paginateFilter(new HerbFilterDTO([
                'toxics' => $data['toxics'],
                'endangereds' => $data['endangereds'],
                'pickparts' => $data['pickparts'],
                'periods' => $data['periods']
            ]), $page, 9);

            return response()->json($herbs);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }

    }

}
