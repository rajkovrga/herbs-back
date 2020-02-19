<?php

namespace App\Http\Controllers;

use App\DTO\Auth\LoginDto;
use App\DTO\Auth\RegisterDto;
use App\Exceptions\AccountVerifyException;
use App\Exceptions\PasswordNotException;
use App\Exceptions\TokenNotFoundException;
use App\Exceptions\VerifyException;
use App\Http\Requests\ForgotPassword;
use App\Http\Requests\LoginUser;
use App\Http\Requests\RegisterUser;
use App\Http\Requests\VerifyAgain;
use App\Notifications\VerifyAccount;
use App\Services\AuthService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{

    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginUser $request)
    {
        $data = $request->validated();
        try {
            $user = $this->authService->login(new LoginDto(['email' => $data['email'], 'password' => $data['password']]));
            return response()->json($user);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'Email is not found'], 404);
        } catch (TokenNotFoundException $er) {
            return response()->json(['message' => 'Token is not created'], 400);
        } catch (VerifyException $er) {
            return response()->json(['message' => 'User not verified'], 401);
        } catch (PasswordNotException $er) {
            return response()->json(['message' => 'Error not found'], 401);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function register(RegisterUser $request)
    {
        $data = $request->validated();
        try {
            $user = $this->authService->register(new RegisterDto(["email" => $data['email'], "password" => $data['password'], "username" => $data['username']]));

            $user->notify(new VerifyAccount($data['email']));

            return response()->json(['message' => 'User registrated, verivicate your account'], 201);
        }
        catch (\PDOException $er) {
            return response()->json(['message' => 'Exist user'], 400);
        }
        catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function verifyAgain(VerifyAgain $request)
    {
        $data = $request->validated();
        try {
            Notification::route('mail', $request->input('email'))
                ->notify((new VerifyAccount($data['email']))->delay(now()->addSeconds(4)));
            return response()->json(['message' => 'Mail sent'], 200);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function forgotPassword(ForgotPassword $request)
    {
        $data = $request->validated();
        try {
            Notification::route('mail', $data['email'])
                ->notify((new ResetPassword($data['email']))->delay(now()->addSeconds(5)));
            return response()->json(['message' => 'Email sent'], 200);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }

    }

    public function resetPassword(Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $email = $request->all()['data'];
        try {
            $token = $this->authService->token($email);
            return response()->json($token);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (VerifyException $er) {
            return response()->json(['message' => 'User is not verify'], 405);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function verify(Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        try {
            $this->authService->verify($request->all()['data']);
            return response()->json(['message' => 'Email verificated']);
        } catch (ModelNotFoundException $er) {
            return response()->json(['message' => 'Not exist this user'], 404);
        } catch (AccountVerifyException $er) {
            return response()->json(['message' => 'Account verified'], 400);
        } catch (\Exception $er) {
            return response()->json(['message' => 'Error'], 500);
        }
    }

}
