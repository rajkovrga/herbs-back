<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Herb extends Model
{
    protected $fillable = [
        'desc',
        'srb_name',
        'lat_name',
        'period_id',
        'toxic',
        'endangered',
        'pickpart_id',
        'url_image'
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function part()
    {
        return $this->belongsTo(Pickpart::class,'pickpart_id','id');
    }

    public function likes()
    {
        return $this->belongsToMany(User::class,'likes','herbs_id','user_id');
    }

    public function comments()
    {
        return $this->belongsToMany(User::class,'comments','herbs_id','user_id')->as("comment")->withPivot('desc','comments_id');
    }
}
