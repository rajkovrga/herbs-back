<?php


namespace App\Services;

use App\DTO\HerbDTO;
use App\DTO\HerbFilterDTO;
use App\Models\Herb;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class HerbsService
{
    public function comments(int $id, int $page = 1)
    {
        $comments = DB::table('herbs')->select(DB::raw('comments.user_id'), DB::raw('users.username'), DB::raw('users.created_at'), DB::raw('users.image_url'), DB::raw('comments.desc'), DB::raw('comment_likes.comments_id'), DB::raw('count(comment_likes.comments_id) as number_likes'))
            ->join('comments', 'herbs.id', '=', 'comments.herbs_id')
            ->leftJoin('comment_likes', 'comment_likes.comments_id', '=', 'comments.id')
            ->join('users', 'users.id', '=', 'comments.user_id')
            ->where('comments.herbs_id', '=', $id)->groupBy('comments.id')->orderBy('number_likes', 'desc')->orderBy('created_at', 'desc')->paginate(5, ['*'], 'page', $page);
        if (count($comments) == 0) {
            throw new ModelNotFoundException('Error with comments for this herb');
        }

        return $comments;
    }

    public function store(HerbDTO $data)
    {
        $herb = new Herb([
            'lat_name' => $data->lat_name,
            'srb_name' => $data->srb_name,
            "toxic" => $data->toxic,
            "endangered" => $data->endangered,
            "desc" => $data->desc,
            "pickpart_id" => $data->pickpart_id,
            'period_id' => $data->period_id
        ]);
        $herb->image_url = $data->url_image;
        $herb->saveOrFail();

        return $herb;
    }

    public function destroy(int $id)
    {
        $row = Herb::query()->findOrFail($id);
        $img = $row->image_url;
        $row->delete();
        return $img;
    }

    public function find(int $id)
    {
        return Herb::query()->with(['period', 'part'])->withCount('likes')->findOrFail($id);
    }

    public function paginate($page = 1, $perPage = 9)
    {
        $data = Herb::query()->paginate($perPage, ['*'], 'page', $page);
        return $data;
    }

    public function update(HerbDTO $herbDTO, int $id)
    {
        $herb = Herb::query()->findOrFail($id);

        $herb->toxic = $herbDTO->toxic;
        $herb->period_id = $herbDTO->period_id;
        $herb->lat_name = $herbDTO->lat_name;
        $herb->srb_name = $herbDTO->srb_name;
        $herb->desc = $herbDTO->desc;
        $herb->pickpart_id = $herbDTO->pickpart_id;
        $herb->endangered = $herbDTO->endangered;

        $herb->saveOrFail();

        return $herb;
    }

    public function paginateFilter(HerbFilterDTO $filterDTO, $page = 1, $perPage = 9)
    {
        // need to add if any array empty

        $data = Herb::query()->with(['period']);

        if (!empty($filterDTO->pickparts)) {
            $data = $data->whereIn('pickpart_id', $filterDTO->pickparts);
        }

        if (!empty($filterDTO->periods)) {
            $data = $data->whereIn('period_id', $filterDTO->periods);
        }

        if (!empty($filterDTO->endangereds)) {
            $data = $data->whereIn('endangered', $filterDTO->endangereds);
        }

        if (!empty($filterDTO->toxics)) {
            $data = $data->whereIn('toxic', $filterDTO->toxics);
        }


        return $data->paginate($perPage, ['*'], 'page', $page);
    }


}

