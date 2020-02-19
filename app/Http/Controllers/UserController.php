<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddPermissionsRequest;
use App\Http\Requests\ChangeRoleRequest;
use App\Http\Requests\ContactRequest;
use App\Http\Requests\NewPermissionRequest;
use App\Http\Requests\NewRoleRequest;
use App\Services\AuthService;
use Illuminate\Support\Facades\Notification;
use App\Exceptions\NotRemovedException;
use App\Exceptions\PasswordNotException;
use App\Http\Requests\CommentHerb;
use App\Http\Requests\ImageRequest;
use App\Http\Requests\PasswordChangeRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\UsernameUserUpdateRequest;
use App\Http\Requests\VerifyEmail;
use App\Models\User;
use App\Notifications\ContactAdmin;
use App\Services\ImageService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Intervention\Image\Response;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use Spatie\Permission\Exceptions\RoleAlreadyExists;

class UserController extends Controller
{

    private $userService;
    private $imageService;
    private $authService;

    public function __construct(UserService $userService, ImageService $imageService, AuthService $authService)
    {
        $this->userService = $userService;
        $this->imageService = $imageService;
        $this->authService = $authService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(int $page = 1)
    {
        try {
            $users = $this->userService->paginate($page, 9);
            return response()->json($users);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 505);
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
            $user = $this->userService->find($id);
            return response()->json($user);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'User not exist'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function image(string $path)
    {
        try {
            $url = public_path().'/../storage/app/images/'.$path;
            return Storage::download($url);

        } catch (\Exception $er) {
            return response()->json(['message' => 'Not found img'], 404);
        }

    }

    public function profile(Request $request)
    {

        try {
            return response()->json($request->user());
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'User not exist'], 404);
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
    public function update(UsernameUserUpdateRequest $request)
    {
        $data = $request->validated();

        if (!$request->user()->can('update-account'))
            abort(403);

        try {

            $user = $this->userService->update($data['username'], $request->user()->id);
            return response()->json($user);
        } catch (PermissionException $er) {
            return response()->json(['message' => 'This user dont have permission for this action']);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'User not exist'], 404);
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
    public
    function destroy(Request $request)
    {
        if (!$request->user()->can('update-account'))
            abort(403);

        try {
            $img = $this->userService->destroy($request->user()->id);
            if ($img != null) {
                Storage::disk('local')->delete($img);
            }

            return response()->json(['message' => 'User deleted'], 200);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'User not exist'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function passwordChange(PasswordChangeRequest $request)
    {
        $data = $request->validated();

        if (!$request->user()->can('change-passowrd'))
            abort(403);

        $user = $request->user();
        try {
            $this->userService->changePassword($user->id, $data['old_pass'], $data['new_pass'], $user->password);
            return response()->json(['message' => 'Password changed success'], 201);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'User not exist'], 404);
        } catch (PasswordNotException $er) {
            return response()->json(['Message' => 'Password not good'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function passwordReset(PasswordResetRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        try {
            $this->userService->resetPassword($user->id, $data['new_pass']);
            return response()->json(['message' => 'Password changed success'], 200);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'User not exist'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function changeEmail(VerifyEmail $request)
    {
        $data = $request->validated();

        if (!$request->user()->can('update-account'))
            abort(403);

        $user = $request->user();
        try {
            $data = $this->userService->changeEmail($data['email'], $user->id);
            return response()->json($data);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'User not exist'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function changeImage(ImageRequest $request)
    {
        $data = $request->validated();

        if (!$request->user()->can('update-avatar'))
            abort(403);

        $image = $request->file('file');
        try {
            $this->imageService->changeImagePhoto(User::class, $image, $request->user()->id, 'USER');
            return response()->json(['message' => 'Image changed']);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'This herb not found'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }


    public function likeHerb(Request $request, int $id)
    {

        if (!$request->user()->can('likes'))
            abort(403);

        try {
            $this->userService->likeHerb($request->user()->id, $id);
            return response()->json(['message' => 'Liked success']);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (\PDOException $er) {
            return response()->json(['message' => 'Like duplicated'], 409);
        } catch (Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function likeComment(Request $request, int $id)
    {

        if (!$request->user()->can('likes'))
            abort(403);

        try {
            $this->userService->likeComment($request->user()->id, $id);
            return response()->json(['message' => 'Liked success']);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (\PDOException $er) {
            return response()->json(['message' => 'Like duplicated'], 409);
        } catch (Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function destroyLikeHerb(Request $request, int $id)
    {
        if (!$request->user()->can('likes'))
            abort(403);

        try {
            $this->userService->destroyLikeHerb($request->user()->id, $id);
            return response()->json(['message' => 'Like deleted']);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (NotRemovedException $er) {
            return response()->json(['message' => 'CommentHerb is not liked'], 400);
        } catch (Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function destroyLikeComment(Request $request, int $id)
    {
        if (!$request->user()->can('likes'))
            abort(403);

        try {
            $this->userService->destroyLikeComment($request->user()->id, $id);
            return response()->json(['message' => 'Like deleted from comment'], 200);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (NotRemovedException $er) {
            return response()->json(['message' => 'Comment is not liked'], 406);
        } catch (Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function commentCreate(CommentHerb $request, int $id)
    {
        $data = $request->validated();

        if (!$request->user()->can('write-comments'))
            abort(403);

        try {
            $this->userService->createComment($request->user()->id, $data['desc'], $id);
            return response()->json(['message' => 'Comment created'], 201);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function commentDestroy(Request $request, int $id)
    {
        if (!$request->user()->can('remove-own-comments'))
            abort(403);

        try {
            $this->userService->destroyComment($request->user()->id, $id, $request->input('comment_id'));
            return response()->json(['message' => 'Comment deleted'], 200);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (NotRemovedException $er) {
            return response()->json(['message' => 'Herb is not commented'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function commentUpdate(CommentHerb $request, int $id)
    {
        $data = $request->validated();

        if (!$request->user()->can('write-comments'))
            abort(403);

        try {
            $this->userService->updateComment($request->user()->id, $data['desc'], $id, $data['comment_id']);
            return response()->json(['message' => 'Comment updated']);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (\PDOException $er) {
            return response()->json(['message' => 'Comment not found'], 400);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function addNewRole(NewRoleRequest $request)
    {
        $data = $request->validated();

        if (!$request->user()->can('change-roles'))
            abort(403);

        try {
            $role = $this->authService->addNewRole($data['name']);
            return response()->json($role);
        } catch (RoleAlreadyExists $er) {
            return response()->json(['message' => 'Role name exist'], 409);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function addNewPermissions(NewPermissionRequest $request)
    {
        $data = $request->validated();

        if (!$request->user()->can('change-roles'))
            abort(403);

        try {
            $permission = $this->authService->addNewPermission($data['name']);
            return response()->json($permission);
        } catch (PermissionAlreadyExists $er) {
            return response()->json(['message' => 'Permission name exist'], 409);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function addPermissionsForRole(AddPermissionsRequest $request)
    {
        $data = $request->validated();

        if (!$request->user()->can('change-roles'))
            abort(403);
        try {
            $this->authService->addPermissionsForRole($data['role_id'], $data['permissions']);
            return response()->json(['message' => 'Added success']);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'Role not found'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function changeRoleToUser(ChangeRoleRequest $request)
    {
        $data = $request->validated();

        if (!$request->user()->can('change-roles'))
            abort(403);

        try {
            $this->authService->changeUserRole($data['user_id'], $data['role_id']);
            return response()->json(['message' => 'Success changed']);
        } catch (ModelNotFoundExceaddPermissionsForRoleption $er) {
            return response()->json(['message' => 'User or role not found'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }


    public function contactAdmin(ContactRequest $request)
    {
        $data = $request->validated();

        try {
            Notification::route('mail', env('MAIL_USERNAME'))
                ->notify((new ContactAdmin($data['title'], $data['desc'], $data['email']))->delay(now()->addSeconds(4)));

            return response()->json(['message' => 'Mail sent']);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function getRoles(Request $request)
    {

        if (!$request->user()->can('get-roles'))
            abort(403);

        try {
            $data = $this->authService->getRoles();
            return response()->json($data);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function getPermissions(Request $request)
    {
        if (!$request->user()->can('get-permissions'))
            abort(403);

        try {
            $data = $this->authService->getPermissions();
            return response()->json($data);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function getPermissionsForRole(int $id)
    {
        try {
            $data = $this->authService->getPermissionsForRole($id);
            return response()->json($data);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'Role not found'], 404);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

}
