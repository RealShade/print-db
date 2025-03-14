<?php

use App\Models\RoleUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::create(app(RoleUser::class)->getTable(), function(Blueprint $table) {
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('role_id');
            $table->primary(['user_id', 'role_id']);
        });

        Schema::create(RoleUser::PERMISSION_ROLE_TABLE, function(Blueprint $table) {
            $table->unsignedTinyInteger('permission_id');
            $table->unsignedTinyInteger('role_id');
            $table->primary(['permission_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists(RoleUser::PERMISSION_ROLE_TABLE);
        Schema::dropIfExists(app(RoleUser::class)->getTable());
    }
};
