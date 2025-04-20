<?php

use App\Models\Catalog;
use App\Models\Part;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        // for each user, who has parts, create new catalog, get it's id and update all parts with null catalog_id
        $users = User::has('parts')->get();
        foreach ($users as $user) {
            $catalog = Catalog::create([
                'name'    => '(old parts)',
                'user_id' => $user->id,
            ]);

            $parts = Part::where('user_id', $user->id)
                ->whereNull('catalog_id')
                ->get();

            foreach ($parts as $part) {
                $part->catalog_id = $catalog->id;
                $part->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Part::whereNotNull('catalog_id')->update(['catalog_id' => null]);
    }

};
