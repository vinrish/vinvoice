<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class UpdateController extends Controller
{
    public function get_version_info(request $request){

        $this->authorizeForUser($request->user('api'), 'update', Setting::class);
        $version = $this->check();

        return response()->json($version);
    }


    /*
    * Return current version (as plain text).
    */
    public function getCurrentVersion(){
        // todo: env file version
        return File::get(base_path().'/version.txt');
    }

    /*
    * Check if a new Update exist.
    */
    public function check()
    {
        $lastVersionInfo = $this->getLastVersion();
        if( version_compare($lastVersionInfo['version'], $this->getCurrentVersion(), ">") )
            return $lastVersionInfo['version'];

        return '';
    }

    private function getLastVersion(){
        $content = file_get_contents('1.0');
//        $content = json_decode($content, true);
        return $content;
    }


    public function viewStep1(Request $request)
    {
        $role = Auth::user()->roles()->first();
        $permission = Role::findOrFail($role->id)->inRole('setting_system');
        if($permission){
            return view('update.viewStep1');
        }
    }

    public function lastStep(Request $request)
    {
        $role = Auth::user()->roles()->first();
        $permission = Role::findOrFail($role->id)->inRole('setting_system');

        if($permission){
            ini_set('max_execution_time', 600); //600 seconds = 10 minutes

            try {

                Artisan::call('config:cache');
                Artisan::call('config:clear');

                Artisan::call('migrate --force');

                $role = Role::findOrFail(1);
                $role->permissions()->detach();

                $permissions = array(
                    0 => 'mail_settings',
                    1 => 'dashboard',
                );

                foreach ($permissions as $permission_slug) {
                    $perm = Permission::firstOrCreate(['name' => $permission_slug]);
                }

                $permissions_data = Permission::pluck('id')->toArray();
                $role->permissions()->attach($permissions_data);

                Artisan::call('module:publish');

            } catch (\Exception $e) {

                return $e->getMessage();

                return 'Something went wrong';
            }

            return view('update.finishedUpdate');
        }
    }
}
