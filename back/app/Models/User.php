<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User  extends Authenticatable implements JwtSubject
{
    use HasRoles;
    use Notifiable;

    protected $guard_name = 'api';

    protected $fillable = [
        'username',
        'email',
        'password'
    ];

    protected $guarded = [
        'remember_token'
    ];

    public function likes()
    {
        return $this->belongsToMany(Herb::class, 'likes', 'user_id', 'herbs_id');
    }
    public function comments()
    {
        return $this->belongsToMany(Herb::class, 'comments', 'user_id', 'herbs_id')->withTimestamps();
    }

    public function commentLikes()
    {
        return $this->belongsToMany(Herb::class, 'comment_likes', 'user_id', 'comments_id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
