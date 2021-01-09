<?php

namespace App\Tests\Utils;

use App\Utils\Validation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ValidationTest extends TestCase
{
    public function testValidateProduct()
    {
        $validation = new Validation();
        $request = new Request();
        $result = $validation->validateProduct($request);
        //Check result is an array
        $this->assertIsArray($result);
    }

    public function testValidateUser()
    {
        $validation = new Validation();
        $request = new Request();
        $result = $validation->validateUser($request);
        //Check result is an array
        $this->assertIsArray($result);
    }
}
