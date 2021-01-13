<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Doc;

/**
 * Class DocController
 * @package App\Http\Controllers
 */
class DocController extends Controller
{
    /**
     * @return string[]
     */
    public function getAll()
    {
        $array = ['error' => ''];

        $docs = Doc::all();

        foreach ($docs as $docKey => $docValue) {
            $docs[$docKey]['fileurl'] = asset('storage/docs/'.$docValue['fileurl']);
        }

        $array['list'] = $docs;

        return $array;
    }
}
