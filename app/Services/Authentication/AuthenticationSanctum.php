<?php
namespace App\Services\Authentication;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegistreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationSanctum implements AuthenticationServiceInterface
{
    public function register($request)
    {
        $currentUser = auth()->user();

        if (!$currentUser || $currentUser->role->nomRole !== 'BOUTIQUIER') {
            return ['success' => false, 'message' => 'Seuls les utilisateurs de rôle "boutiquier" peuvent s\'enregistrer.', 'status' => 403];
        }

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'photo' => $request->photo,
            'login' => $request->login,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'active' => $request->active,
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return ['success' => true, 'user' => $user, 'token' => $token];
    }

    public function login($credentials)
    {
        return $this->authenticate($credentials);
    }

    public function authenticate($credentials)
    {
        $user = User::where('login', $credentials['login'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return ['success' => false, 'message' => 'Échec lors de l\'authentification.'];
        }

        $token = $user->createToken('appToken')->plainTextToken;

        return [
            'success' => true,
            'token' => $token,
            'user' => $user
        ];
    }

    public function refreshToken($request)
    {
        // Avec Sanctum, le rafraîchissement des tokens se fait en obtenant un nouveau token via login.
        $credentials = $request->only('login', 'password');
        return $this->authenticate($credentials);
    }

    public function logout($user): array
    {
        if ($user) {
            $user->tokens()->delete();
            return ['success' => true, 'message' => 'Déconnexion réussie. Vous devez vous reconnecter pour accéder à l\'application.'];
        }

        return ['success' => false, 'message' => 'Utilisateur non authentifié.'];
    }
}
