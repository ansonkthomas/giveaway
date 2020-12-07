<?php

namespace App\Controller;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;

class UtilityController
{
    /**
     * Convert object to json
     */
    public static function objectToJsonSerialize($object)
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $data = $serializer->serialize($object, 'json');

        return $data;
    }

    /**
     * Convert object to array
     *
     * @return array $data
     */
    public static function objctToArrayNormalize($object)
    {
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers);
        $data = $serializer->normalize($object);

        return $data;
    }

    public static function transformJsonBody(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }
        $request->request->replace($data);

        return $request;
    }
}
