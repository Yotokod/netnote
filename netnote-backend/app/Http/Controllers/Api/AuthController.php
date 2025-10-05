<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\School;
use App\Models\Country;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class AuthController extends Controller
{
    /**
     * Vérifier si l'utilisateur est un compte démo
     */
    private function isDemoUser($email)
    {
        $demoEmails = [
            'marcosseko00@gmail.com',
            'admin@demo-ecole.netnote.cm',
            'finance@demo-ecole.netnote.cm',
            'prof@demo-ecole.netnote.cm'
        ];
        
        return in_array($email, $demoEmails);
    }

    /**
     * Connexion utilisateur
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
                'remember_me' => 'boolean',
                'two_factor_code' => 'nullable|string|size:6'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = $request->only('email', 'password');
            
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect'
                ], 401);
            }

            $user = Auth::user();
            
            // Vérifier si l'utilisateur est actif
            if (!$user->is_active) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte a été désactivé. Contactez l\'administrateur.'
                ], 403);
            }

            // Vérification 2FA si activée (désactivée pour les comptes démo)
            if ($user->is_2fa_enabled && !$this->isDemoUser($user->email)) {
                if (!$request->two_factor_code) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Code de double authentification requis',
                        'requires_2fa' => true
                    ], 200);
                }

                $google2fa = new Google2FA();
                if (!$google2fa->verifyKey($user->google2fa_secret, $request->two_factor_code)) {
                    Auth::logout();
                    return response()->json([
                        'success' => false,
                        'message' => 'Code de double authentification invalide'
                    ], 401);
                }
            }

            $token = $user->createToken('NetNote')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie',
                'data' => [
                    'user' => $user->load('schools'),
                    'token' => $token
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la connexion',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Inscription utilisateur
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'global_role' => 'required|in:super_admin,admin,teacher,finance,student'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'global_role' => $request->global_role,
                'is_active' => true
            ]);

            $token = $user->createToken('NetNote')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la déconnexion',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Rafraîchir le token
     */
    public function refresh(Request $request)
    {
        try {
            $user = $request->user();
            $request->user()->currentAccessToken()->delete();
            $token = $user->createToken('NetNote')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Token rafraîchi avec succès',
                'data' => [
                    'token' => $token
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du rafraîchissement',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Informations utilisateur connecté
     */
    public function me(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $request->user()->load('schools')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des informations',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Mot de passe oublié
     */
    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email invalide',
                    'errors' => $validator->errors()
                ], 422);
            }

            $status = Password::sendResetLink($request->only('email'));

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lien de réinitialisation envoyé par email'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi du lien'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la demande de réinitialisation',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|string|min:8|confirmed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));
                    $user->save();
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mot de passe réinitialisé avec succès'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réinitialisation'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réinitialisation',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Activer la double authentification
     */
    public function enable2FA(Request $request)
    {
        try {
            $google2fa = new Google2FA();
            $user = $request->user();
            
            if ($user->is_2fa_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'La double authentification est déjà activée'
                ], 400);
            }

            $secret = $google2fa->generateSecretKey();
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            );

            $user->update(['google2fa_secret' => $secret]);

            return response()->json([
                'success' => true,
                'message' => 'Secret généré. Scannez le QR code avec votre app d\'authentification',
                'data' => [
                    'secret' => $secret,
                    'qr_code_url' => $qrCodeUrl
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'activation de la 2FA',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Vérifier et activer la 2FA
     */
    public function verify2FA(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|size:6'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code invalide',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            $google2fa = new Google2FA();

            if (!$google2fa->verifyKey($user->google2fa_secret, $request->code)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code de vérification invalide'
                ], 400);
            }

            $user->update(['is_2fa_enabled' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Double authentification activée avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Désactiver la 2FA
     */
    public function disable2FA(Request $request)
    {
        try {
            $user = $request->user();
            
            $user->update([
                'is_2fa_enabled' => false,
                'google2fa_secret' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Double authentification désactivée'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la désactivation',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'profile_data' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->update($request->only(['name', 'email', 'phone', 'profile_data']));

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès',
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mot de passe actuel incorrect'
                ], 400);
            }

            $user->update(['password' => Hash::make($request->password)]);

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe changé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du changement de mot de passe',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier invalide',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            
            // Supprimer l'ancien avatar s'il existe
            if ($user->profile_data && isset($user->profile_data['avatar'])) {
                Storage::disk('public')->delete($user->profile_data['avatar']);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            
            $profileData = $user->profile_data ?? [];
            $profileData['avatar'] = $path;
            
            $user->update(['profile_data' => $profileData]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar uploadé avec succès',
                'data' => [
                    'avatar_url' => Storage::url($path)
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Inscription d'école
     */
    public function schoolRegistration(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'school_name' => 'required|string|max:255',
                'subdomain' => 'required|string|max:50|unique:schools,subdomain|regex:/^[a-z0-9-]+$/',
                'admin_name' => 'required|string|max:255',
                'admin_email' => 'required|email|unique:users,email',
                'admin_phone' => 'nullable|string|max:20',
                'password' => 'required|string|min:8|confirmed',
                'country_id' => 'required|exists:countries,id',
                'city_id' => 'required|exists:cities,id',
                'template_id' => 'nullable|exists:templates,id',
                'bulletin_template_id' => 'nullable|exists:templates,id',
                'plan_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Créer l'utilisateur admin
            $admin = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'phone' => $request->admin_phone,
                'password' => Hash::make($request->password),
                'global_role' => 'admin',
                'is_active' => true
            ]);

            // Créer l'école
            $school = School::create([
                'name' => $request->school_name,
                'slug' => Str::slug($request->school_name),
                'subdomain' => $request->subdomain,
                'country_id' => $request->country_id,
                'city_id' => $request->city_id,
                'template_id' => $request->template_id,
                'bulletin_template_id' => $request->bulletin_template_id,
                'is_active' => true
            ]);

            // Associer l'admin à l'école
            $school->users()->attach($admin->id, [
                'role_at_school' => 'admin',
                'is_active' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'École créée avec succès',
                'data' => [
                    'school' => $school,
                    'admin' => $admin,
                    'subdomain_url' => 'https://' . $request->subdomain . '.' . config('app.domain')
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'école',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Contact
     */
    public function contact(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'subject' => 'required|string|max:255',
                'message' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Ici vous pouvez envoyer un email ou sauvegarder en base
            // Mail::to(config('mail.contact'))->send(new ContactMail($request->all()));

            return response()->json([
                'success' => true,
                'message' => 'Message envoyé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi du message',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }
}
