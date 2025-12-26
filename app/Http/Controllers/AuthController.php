<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,dns',
            'password' => 'required|min:5|max:20',
        ], [
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener entre 5 y 20 caracteres.',
            'password.max' => 'La contraseña debe tener entre 5 y 20 caracteres.',
        ]);

        $user = User::where('email', $request->email)->first();

        $valid = false;
        if ($user) {
            $stored = (string) $user->password;
            if (str_starts_with($stored, '$2y$')) {
                try {
                    $valid = Hash::check($request->password, $stored);
                } catch (\RuntimeException $e) {
                    $valid = false;
                }
            } else {
                $valid = $request->password === $stored;
                if ($valid) {
                    $user->password = $request->password;
                    $user->save();
                }
            }
        }

        if ($user && $valid) {
            Auth::login($user);

            // Registrar inicio de sesión exitoso
            Auditoria::registrar(
                'inicio_sesion',
                'autenticacion',
                'Inicio de sesión exitoso',
                null,
                ['user_agent' => $request->header('User-Agent')]
            );

            if ($user->isAdmin()) {
                return redirect()->route('admin.index');
            }

            return redirect()->route('home');
        }

        // Registrar intento fallido
        Auditoria::registrar(
            'intento_fallido',
            'autenticacion',
            'Intento fallido de inicio de sesión',
            null,
            ['email' => $request->email, 'user_agent' => $request->header('User-Agent')]
        );

        return back()->withErrors(['email' => 'Credenciales incorrectas'])->withInput();
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|string|max:50|unique:usuarios,nombre_usuario',
            'nombre_completo' => 'required|string|max:100',
            'email' => 'required|email:rfc,dns|max:100|unique:usuarios,email',
            'telefono' => 'nullable|digits_between:9,15',
            'direccion' => 'nullable|string|max:200',
            'password' => [
                'required',
                'string',
                'min:5',
                'max:20',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            ],
        ], [
            'nombre_usuario.required' => 'El nombre de usuario es obligatorio',
            'nombre_usuario.unique' => 'Este nombre de usuario ya está en uso',
            'nombre_completo.required' => 'El nombre completo es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser una dirección válida',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 5 caracteres',
            'password.max' => 'La contraseña no puede tener más de 20 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password.regex' => 'La contraseña debe contener al menos una letra mayúscula, una minúscula, un número y un carácter especial (@$!%*?&)',
            'telefono.digits_between' => 'Ingrese un número de teléfono válido (solo números, entre 9 y 15 dígitos)',
        ]);

        $user = User::create([
            'nombre_usuario' => $request->nombre_usuario,
            'nombre_completo' => $request->nombre_completo,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'password' => Hash::make($request->password),
            'rol' => 'usuario',
            'estado' => 'activo'
        ]);

        Auth::login($user);

        // Registrar nuevo usuario
        Auditoria::registrar(
            'registro',
            'autenticacion',
            'Nuevo usuario registrado',
            null,
            [
                'user_id' => $user->id,
                'email' => $user->email,
                'user_agent' => $request->header('User-Agent')
            ]
        );

        return redirect()->route('home');
    }

    public function logout()
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        $userName = $user ? $user->nombre_usuario : 'Desconocido';

        // Registrar cierre de sesión
        if ($user) {
            Auditoria::registrar(
                'cierre_sesion',
                'autenticacion',
                'El usuario ha cerrado sesión',
                null,
                ['user_id' => $userId]
            );
        }

        Auth::logout();
        Session::flush();
        return redirect()->route('home');
    }
}
