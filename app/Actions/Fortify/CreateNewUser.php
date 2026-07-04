<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
 public function create(array $input): User
{
    // 1. Validasi data yang datang dari Form Blade
    Validator::make($input, [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'address' => ['required', 'string', 'max:500'], // TAMBAHKAN INI
        'no_tlp' => ['required', 'string', 'max:15'],  // TAMBAHKAN INI
        'password' => $this->passwordRules(),
    ])->validate();

    // 2. Simpan ke Database
    $user = User::create([
        'name' => $input['name'],
        'email' => $input['email'],
        'address' => $input['address'], // TAMBAHKAN INI
        'no_tlp' => $input['no_tlp'],   // TAMBAHKAN INI
        'password' => Hash::make($input['password']),
    ]);

    // 3. Berikan Role Otomatis
    // Gunakan 'Customer' (huruf C besar) sesuai yang ada di sidebar Filament kamu
    $user->assignRole('Customer'); 

    return $user;
}
}
