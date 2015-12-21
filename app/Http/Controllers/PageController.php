<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
    /**
     * Get rankings page
     *
     * @return Response
     */
    public function getRankings()
    {
        $users = \App\Character::where('gm', '<', 4)->orderBy('level', 'DESC')->paginate(10);
        return $users;
    }

    /**
    * Get the news page
    *
    * @return Response
    */
    public function getNews() {
        return \App\News::all();
    }
}