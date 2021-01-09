<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;

class Validation
{

    /**
     * Validate product parameters
     *
     * @param Request $request
     *
     * @return array $validate
     */
    public function validateProduct(Request $request)
    {
        $validate = array();
        if (empty($request->get("name"))) {
            array_push($validate, array("name" => "A product name is required"));
        }
        if (empty($request->get("type"))) {
            array_push($validate, array("type" => "A product type is required"));
        }

        return $validate;
    }

    /**
     * Validate user parameters
     *
     * @param array $request

     * @return array $validate
     */
    public function validateUser(Request $request)
    {
        $validate = array();
        if (empty($request->get("username"))) {
            array_push($validate, array("username" => "A username is required"));
        }
        if (empty($request->get("password"))) {
            array_push($validate, array("password" => "A password is required"));
        }

        return $validate;
    }
}
