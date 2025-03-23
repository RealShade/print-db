<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        $admin = User::create([
            'name'     => 'Admin',
            'email'    => config('app.admin_email'),
            'password' => Hash::make(config('app.admin_password')),
            'status'   => UserStatus::ACTIVE,
        ]);

        $admin->assignRole(UserRole::ADMIN);
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        User::where('email', config('app.admin_email'))->first()->delete();
    }

};
