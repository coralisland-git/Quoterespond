<?php

use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pages')->truncate();

        DB::table('pages')->insert([
            'code' => 'users-users',
            'folder' => '',
            'file' => '',
            'name' => 'Users',
            'icon' => 'fa fa-user',
            'tpl' => '',
            'public' => 0,
        ]);

        DB::table('pages')->insert([
            'code' => 'users-live',
            'folder' => 'users',
            'file' => 'live',
            'name' => 'Live',
            'icon' => '',
            'tpl' => '',
            'public' => 0,
        ]);

        DB::table('pages')->insert([
            'code' => 'users-canceled',
            'folder' => 'users',
            'file' => 'canceled',
            'name' => 'Canceled',
            'icon' => '',
            'tpl' => '',
            'public' => 0,
        ]);

        DB::table('pages')->insert([
            'code' => 'users-profile',
            'folder' => 'users',
            'file' => 'profile',
            'name' => 'Profile',
            'icon' => 'fa fa-chevron-right',
            'tpl' => '',
            'public' => 0,
        ]);

        DB::table('pages')->insert([
            'code' => 'plans-list',
            'folder' => 'plans',
            'file' => 'list',
            'name' => 'Payment Plans',
            'icon' => 'fa fa-credit-card',
            'tpl' => '',
            'public' => 0,
        ]);

        DB::table('pages')->insert([
            'code' => 'plans-user',
            'folder' => 'plans',
            'file' => 'user',
            'name' => 'Payment Plans',
            'icon' => 'fa fa-credit-card',
            'tpl' => '',
            'public' => 0,
        ]);

        // DB::table('pages')->insert([
        //     'code' => 'matchings-list',
        //     'folder' => 'matchings',
        //     'file' => 'list',
        //     'name' => 'Matchings',
        //     'icon' => 'fa fa-cogs',
        //     'tpl' => '',
        //     'public' => 0,
        // ]);

        DB::table('pages')->insert([
            'code' => 'yelp-dashboard',
            'folder' => 'dashboard',
            'file' => 'user',
            'name' => 'Dashboard',
            'icon' => 'fa fa-home',
            'tpl' => '',
            'public' => 0,
        ]);

        DB::table('pages')->insert([
            'code' => 'plans-info',
            'folder' => 'plans',
            'file' => 'info',
            'name' => 'Billing',
            'icon' => 'fa fa-clipboard',
            'tpl' => '',
            'public' => 0,
        ]);




        DB::table('pages_access')->truncate();

        DB::table('pages_access')->insert([
            'code' => 'users-users',
            'users_type' => 2,
        ]);

        DB::table('pages_access')->insert([
            'code' => 'users-live',
            'users_type' => 2,
        ]);

        DB::table('pages_access')->insert([
            'code' => 'users-canceled',
            'users_type' => 2,
        ]);

        DB::table('pages_access')->insert([
            'code' => 'plans-list',
            'users_type' => 2,
        ]);

        // DB::table('pages_access')->insert([
        //     'code' => 'matchings-list',
        //     'users_type' => 2,
        // ]);

        DB::table('pages_access')->insert([
            'code' => 'yelp-dashboard',
            'users_type' => 1,
        ]);

        DB::table('pages_access')->insert([
            'code' => 'plans-info',
            'users_type' => 1,
        ]);



        DB::table('pages_menu')->truncate();

        DB::table('pages_menu')->insert([
            'pages_code' => 'users-users',
            'parents_code' => '',
            'plans' => 'none',
            'main' => 0,
            'pos' => 1,
        ]);

        DB::table('pages_menu')->insert([
            'pages_code' => 'users-live',
            'parents_code' => 'users-users',
            'plans' => 'none',
            'main' => 1,
            'pos' => 1,
        ]);

        DB::table('pages_menu')->insert([
            'pages_code' => 'users-canceled',
            'parents_code' => 'users-users',
            'plans' => 'none',
            'main' => 0,
            'pos' => 2,
        ]);

        DB::table('pages_menu')->insert([
            'pages_code' => 'plans-list',
            'parents_code' => '',
            'plans' => 'none',
            'main' => 0,
            'pos' => 2,
        ]);

        // DB::table('pages_menu')->insert([
        //     'pages_code' => 'matchings-list',
        //     'parents_code' => '',
        //     'plans' => 'none',
        //     'main' => 0,
        //     'pos' => 3,
        // ]);

        DB::table('pages_menu')->insert([
            'pages_code' => 'yelp-dashboard',
            'parents_code' => '',
            'plans' => 'yelp-quoterespond',
            'main' => 1,
            'pos' => 1,
        ]);

        DB::table('pages_menu')->insert([
            'pages_code' => 'plans-info',
            'parents_code' => '',
            'plans' => 'yelp-quoterespond',
            'main' => 0,
            'pos' => 2,
        ]);
    }
}
