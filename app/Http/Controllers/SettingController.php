<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    //-------------- Update  Settings ---------------\\

    public function update(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'update', Setting::class);

        $setting = Setting::findOrFail($id);
        $currentAvatar = $setting->logo;
        if ($request->logo != $currentAvatar) {

            $image = $request->file('logo');
            $path = public_path() . '/images';
            $filename = rand(11111111, 99999999) . $image->getClientOriginalName();

            $image_resize = Image::make($image->getRealPath());
            $image_resize->resize(80, 80);
            $image_resize->save(public_path('/images/' . $filename));

            $userPhoto = $path . '/' . $currentAvatar;
            if (file_exists($userPhoto)) {
                if ($setting->logo != 'logo-default.png') {
                    @unlink($userPhoto);
                }
            }
        } else {
            $filename = $currentAvatar;
        }
        if ($request['currency'] != 'null') {
            $currency = $request['currency'];
        } else {
            $currency = null;
        }

        if ($request['default_language'] != 'null') {
            $default_language = $request['default_language'];
        } else {
            $default_language = 'en';
        }

        Setting::whereId($id)->update([
            'currency_id' => $currency,
            'email' => $request['email'],
            'default_language' =>  $default_language,
            'CompanyName' => $request['CompanyName'],
            'CompanyPhone' => $request['CompanyPhone'],
            'CompanyAdress' => $request['CompanyAdress'],
            'footer' => $request['footer'],
            'developed_by' => $request['developed_by'],
            'logo' => $filename,
        ]);

        $this->setEnvironmentValue([
            'APP_TIMEZONE' => $request['timezone'] !== null?'"' . $request['timezone'] . '"':'"UTC"',
        ]);

        Artisan::call('config:cache');
        Artisan::call('config:clear');

        return response()->json(['success' => true]);
    }

    //-------------- Get All Settings ---------------\\

    public function getSettings(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'view', Setting::class);

        $settings = Setting::where('deleted_at', '=', null)->first();
        if ($settings) {
            if ($settings->currency_id) {
                if (Currency::where('id', $settings->currency_id)->where('deleted_at', '=', null)->first()) {
                    $item['currency_id'] = $settings->currency_id;
                } else {
                    $item['currency_id'] = '';
                }
            } else {
                $item['currency_id'] = '';
            }

            if ($settings->sms_gateway) {
                if (sms_gateway::where('id', $settings->sms_gateway)->where('deleted_at', '=', null)->first()) {
                    $item['sms_gateway'] = $settings->sms_gateway;
                } else {
                    $item['sms_gateway'] = '';
                }
            } else {
                $item['sms_gateway'] = '';
            }

            $item['id'] = $settings->id;
            $item['email'] = $settings->email;
            $item['CompanyName'] = $settings->CompanyName;
            $item['CompanyPhone'] = $settings->CompanyPhone;
            $item['CompanyAdress'] = $settings->CompanyAdress;
            $item['logo'] = $settings->logo;
            $item['footer'] = $settings->footer;
            $item['developed_by'] = $settings->developed_by;
            $item['default_language'] = $settings->default_language;
            $item['timezone'] = env('APP_TIMEZONE') == null?'UTC':env('APP_TIMEZONE');

            $zones_array = array();
            $timestamp = time();
            foreach(timezone_identifiers_list() as $key => $zone){
                date_default_timezone_set($zone);
                $zones_array[$key]['zone'] = $zone;
                $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
                $zones_array[$key]['label'] = $zones_array[$key]['diff_from_GMT'] . ' - ' . $zones_array[$key]['zone'];
            }

            $Currencies = Currency::where('deleted_at', null)->get(['id', 'name']);

            return response()->json([
                'settings' => $item ,
                'currencies' => $Currencies,
                'zones_array' => $zones_array,
            ], 200);
        } else {
            return response()->json(['statut' => 'error'], 500);
        }
    }


    //-------------- Clear_Cache ---------------\\

    public function Clear_Cache(Request $request)
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
    }


    //-------------- Set Environment Value ---------------\\

    public function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        $str .= "\r\n";
        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {

                $keyPosition = strpos($str, "$envKey=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

                if (is_bool($keyPosition) && $keyPosition === false) {
                    // variable doesnot exist
                    $str .= "$envKey=$envValue";
                    $str .= "\r\n";
                } else {
                    // variable exist
                    $str = str_replace($oldLine, "$envKey=$envValue", $str);
                }
            }
        }

        $str = substr($str, 0, -1);
        if (!file_put_contents($envFile, $str)) {
            return false;
        }

        app()->loadEnvironmentFrom($envFile);

        return true;
    }
}
