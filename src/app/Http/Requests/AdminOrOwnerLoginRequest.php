<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AdminOrOwnerLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public string $role;

    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:191',
            'password' => 'required|string|min:8|max:191',
        ];
    }

    public function role(): string
    {
        if ($this->routeIs('admin*')) {
            return 'admin';
        } elseif ($this->routeIs('owner*')) {
            return 'owner';
        }

        abort(404);
    }

    public function authenticate()
    {
        $role = $this->role();
        $key = 'login|'.$this->email.'|'.$this->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'ログイン試行回数が多すぎます。しばらく待ってください。',
            ]);
        }

        if (!Auth::guard($role)->attempt($this->only('email', 'password'))) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages([
                'email' => 'ログイン情報が登録されていません',
            ]);
        }

        RateLimiter::clear($key);

        foreach (['admin', 'owner'] as $guard) {
            if ($guard !== $role) {
                Auth::guard($guard)->logout();
            }
        }
    }

    public function messages(): array
    {
        return [
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスの形式で入力してください',
            'password.required' => 'パスワードを入力してください',
            'password.min' => 'パスワードは8文字以上で入力してください',
        ];
    }
}
