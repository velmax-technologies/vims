<?php

namespace Modules\Setting\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponseFormatTrait;
use Modules\Setting\Http\Requests\SettingRequest;
use Modules\Setting\Transformers\SettingResource;

class SettingController extends Controller
{
    use ApiResponseFormatTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Setting::all();
        return (SettingResource::collection($settings))->additional($this->preparedResponse('index'));
    }


    /**
     * Show the specified resource.
     */
    public function show($key)
    {
        $setting = Setting::where('key', $key)->firstOrFail();
        return (new SettingResource($setting))->additional($this->preparedResponse('show'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SettingRequest $request) {
        $request->validated();
       

        foreach ($request->settings as $key => $settingData) {
            //return $settingData["key"];
            $setting = Setting::where('key', $settingData["key"])->first();
            if ($setting && !$setting->locked) {
                $setting->value = $settingData["value"];
                $setting->save();
            }
        }
        $settings = Setting::all();
        return (SettingResource::collection($settings))->additional($this->preparedResponse('index'));
        //return (new SettingResource($setting))->additional($this->preparedResponse('update'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
