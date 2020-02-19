<?php


namespace App\Services;

use App\DTO\UserDto;
use App\Exceptions\DuplicateExtension;
use App\Exceptions\LikeExistException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotRemovedException;
use App\Exceptions\PasswordNotException;
use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;


class UserService
{
    protected $hasher;
    protected $imageService;

    public function __construct(Hasher $hasher, ImageService $imageService)
    {
        $this->hasher = $hasher;
        $this->imageService = $imageService;
    }

    public function update($username, $id)
    {
        $user = User::query()->findOrFail($id);
        $user->username = $username;
        $user->saveOrFail();
        return $user;
    }

    public function find(int $id)
    {
        $row = User::query()->findOrFail($id);
        return $row;
    }

    public function destroy(int $id)
    {
        $user = User::query()->findOrFail($id);
        $img = $user->image_url;
        $user->removeRole($user->roles()->pluck('name'));
        $user->delete();
        return $img;
    }

    public function paginate($page = 1, $perPage = 9)
    {
        $data = User::query()->paginate($perPage, ['*'], 'page', $page);
        return $data;
    }

    public function changePassword(int $id, string $oldPass, string $newPass, string $hash)
    {
        if (!$this->hasher->check($oldPass, $hash)) {
            throw new PasswordNotException('Old password is not good', 404);
        }
        $user = User::query()->findOrFail($id);
        $user->password = $this->hasher->make($newPass);
        $user->saveOrFail();
    }

    public function resetPassword(int $id, string $password)
    {
        $user = User::query()->findOrFail($id);
        $user->password = $this->hasher->make($password);
        $user->saveOrFail();
    }

    public function changeEmail(string $new_email, int $id)
    {
        $user = User::query()->findOrFail($id);
        $user->email = $new_email;
        $user->email_verified_at = null;
        $user->saveOrFail();
        return $user;
    }


    public function likeHerb(int $userId, int $herbId)
    {
        $user = User::query()->findOrFail($userId);
        $like = $user->likes()->attach($herbId, ['id' => $userId . $herbId]);

    }

    public function likeComment(int $userId, int $commentId)
    {
        $user = User::query()->findOrFail($userId);
        $like = $user->commentLikes()->attach($commentId, ['id' => $userId . $commentId]);
    }

    public function destroyLikeHerb(int $userId, int $herbId)
    {
        $user = User::query()->findOrFail($userId);
        $user->likes()->detach($herbId);
    }

    public function destroyLikeComment(int $userId, int $commentId)
    {
        $user = User::query()->findOrFail($userId);
        $like = $user->commentLikes()->detach($commentId);

        if (!$like) {
            throw new NotRemovedException('Herb is not liked');
        }
    }

    public function createComment(int $userId, string $desc, int $herbId)
    {
        $user = User::query()->findOrFail($userId);
        $comment = $user->comments()->attach($herbId,['desc' => $desc]);

    }

    public function destroyComment(int $userId, int $herbId, int $commentId)
    {
      $user = User::query()->findOrFail($userId);
      $comment = $user->comments()->wherePivot('id', '=' , $commentId)->detach($herbId);
      if (!$comment) {
          throw new NotRemovedException('Herb is not commented');
      }
    }

    public function updateComment(int $userId, string $desc, int $herb_id, int $comment_id)
    {
        $user = User::query()->findOrFail($userId);
        $comment = $user->comments()->wherePivot('id', '=' , $comment_id)->updateExistingPivot($herb_id,['desc' => $desc]);

        if(!$comment)
        {
            throw new \PDOException('Comment not found');
        }

        $user->saveOrFail();
    }
}
