<?php

namespace App\Http\Controllers;

use App\Models\Billet;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Class BilletController
 * @package App\Http\Controllers
 */
class BilletController extends Controller
{
    /**
     * @param Request $request
     * @return string[]
     */
    public function getAll(Request $request)
    {
        $array = ['error' => ''];

        $property = $request->input('property');

        if(!$property){
            $array['error'] = 'A propriedade é necessária';
            return $array;
        }

        $user = auth()->user();

        $unit = Unit::where('id', $property)
            ->where('id_owner', $user['id'])
            ->count();

        if($unit <= 0){
            $array['error'] = 'Esta unidade não é sua';
            return $array;
        }

        $billets = Billet::where('id_unit', $property)->get();

        foreach ($billets as $billetKey => $billetValue) {
            $billets[$billetKey]['fileurl'] = asset('storage/billets/'.$billetValue['fileurl']);
        }

        $array['list'] = $billets;

        return $array;
    }
}
