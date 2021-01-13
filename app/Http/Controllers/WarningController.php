<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Warning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * Class WarningController
 * @package App\Http\Controllers
 */
class WarningController extends Controller
{
    /**
     * @param Request $request
     * @return string[]
     */
    public function getMyWarnings(Request $request)
    {
        $array = ['error' => ''];

        $property = $request->input('property');

        if (!$property) {
            $array['error'] = 'A propriedade é necessária';
            return $array;
        }

        $user = auth()->user();

        $unit = Unit::where('id', $property)
            ->where('id_owner', $user['id'])
            ->count();

        if ($unit <= 0) {
            $array['error'] = 'Esta unidade não é sua';
            return $array;
        }

        $warnings = Warning::where('id_unit', $property)
            ->orderBy('datecreated', 'DESC')
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($warnings as $warningKey => $warningValue) {
            $warnings[$warningKey]['datecreated'] = date('d/m/Y', strtotime($warnings[$warningKey]['datecreated']));
            $photoList = [];

            $photos = explode(',', $warnings[$warningKey]['photos']);
            foreach ($photos as $photo) {
                if (!empty($photos)) {
                    $photoList[] = asset('storage/photos/' . $photo);
                }
            }

            $warnings[$warningKey]['photos'] = $photoList;
        }

        $array['list'] = $warnings;

        return $array;
    }

    /**
     * @param Request $request
     * @return string[]
     */
    public function addWarningFile(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'photo' => 'required|file|mimes:jpg,png'
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $file = $request->file('photo')->store('public/photos');

        $array['photo'] = asset(Storage::url($file));

        return $array;
    }

    /**
     * @param Request $request
     * @return string[]
     */
    public function setWarning(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'property' => 'required',
            'title' => 'required'
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $title = $request->input('title');
        $property = $request->input('property');
        $list = $request->input('list');

        $newWarn = new Warning();
        $newWarn->id_unit = $property;
        $newWarn->title = $title;
        $newWarn->status = 'IN_REVIEW';
        $newWarn->datecreated = date('Y-m-d');

        if ($list && is_array($list)) {
            $photos = [];
            foreach ($list as $listItem) {
                $url = explode('/', $listItem);
                $photos[] = end($url);
            }
            $newWarn->photos = implode(',', $photos);
        }else{
            $newWarn->photos = '';
        }
        $newWarn->save();

        return $array;
    }
}
