<?php

namespace App\Http\Requests;
use Illuminate\Http\Request;

class CRUDRequest extends Request {

    public function all()
    {
    	 return array_replace_recursive(
            parent::all(),
            $this->route()->parameters()
        );
    }
}