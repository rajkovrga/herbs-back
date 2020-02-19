<?php

namespace App\Services;

use App\DTO\Auth\LoginDto;
use App\DTO\Auth\RegisterDto;
use App\Exceptions\AccountVerifyException;
use App\Exceptions\PasswordNotException;
use App\Exceptions\TokenNotFoundException;
use App\Exceptions\VerifyException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Hashing\Hasher;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\JWTAuth;

class AuthService
{

    private $jwtAuth;
    private $hasher;

    public function __construct(JWTAuth $jwtAuth, Hasher $hasher)
    {
        $this->jwtAuth = $jwtAuth;
        $this->hasher = $hasher;
    }

    public function login(LoginDto $data)
    {
        $user = User::query()->where('email', '=', $data->email)->firstOrFail();

        if($user->email_verified_at == null)
        {
            throw new VerifyException();
        }

        if (!$this->hasher->check($data->password, $user->password)) {
            throw new PasswordNotException('Password is not valid', 403);
        }

        return $this->createToken($user);

    }

    public function createToken($user)
    {
        $token = $this->jwtAuth->fromSubject($user);

        if (!$token) {
            throw new TokenNotFoundException("Token is not created");
        }

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 120 * 60
        ];
    }

    public function token(string $email)
    {
        $user = User::query()->where('email', $email)->firstOrFail();
        if ($user->email_verified_at == null) {
            throw new VerifyException();
        }
        return $this->createToken($user);
    }

    public function register(RegisterDto $data)
    {
        $user = new User([
            'email' => $data->email,
            'username' => $data->username,
            "password" => bcrypt($data->password)
        ]);
        $user->saveOrFail();
        $user->assignRole('user');

        return $user;
    }

    public function verify(string $email)
    {
        $user = User::query()->where('email', '=', $email)->firstOrFail();

        if ($user->email_verified_at != null) {
            throw new AccountVerifyException();
        }

        $user->email_verified_at = Carbon::now();
        $user->saveOrFail();
    }

    //roles

    public function addNewRole(string $name)
    {
        $role = Role::create(['name' => $name]);

        return $role;
    }
    public function addNewPermission(string $name)
    {
        $permission = Permission::create(['name' => $name]);
        return $permission;
    }

    public function changeUserRole(int $userId, int $roleId)
    {
        $user = User::query()->findOrFail($userId);
        $user->roles()->sync([$roleId]);
    }

    public function addPermissionsForRole(int $roleId, $permissions)
    {
        $role = Role::findById($roleId);
        $role->givePermissionTo($permissions);
    }

    public function getPermissions()
    {
        return Permission::all();
    }

    public function getRoles()
    {
        return Role::all();
    }

    public function getPermissionsForRole(int $id)
    {
        $role = Role::findById($id);

        return $role->permissions()->get();
    }

}
