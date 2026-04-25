<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use App\Rules\ValidNif;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'company' => 'required|string|max:255',
            'nif' => ['required', 'string', 'max:20', 'unique:companies,nif', new ValidNif],
        ])->validate();

        $company = Company::create([
            'name' => $input['company'],
            'nif' => $input['nif'],
        ]);

        $adminRole = Role::where('name', 'Admin')->first();

        return User::create([
            'name'       => $input['name'],
            'email'      => $input['email'],
            'password'   => $input['password'],
            'company_id' => $company->id,
            'role_id'    => $adminRole?->id,
        ]);


    }
}
