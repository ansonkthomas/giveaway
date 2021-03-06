<?php

namespace App\Controller;

use App\Entity\User;
use App\Utils\FormatData;
use App\Utils\Validation;
use App\Service\ApiResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends AbstractController
{
    /**
     * Register a user
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, FormatData $formatData, Validation $validation, ApiResponse $apiResponse)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $request = $formatData->transformJsonBody($request);
        $username = $request->get('username');
        $password = $request->get('password');

        try {
            if (!$request) {
                $apiResponse->throwBadRequest();
            }
            //Validate the user properties
            $validate = $validation->validateUser($request);
            if (count($validate)) {
                $apiResponse->setValidationStatusCode();
                $data = [
                    "message" => $validate
                ];
            } else {
                //Create an instance of User entity
                $user = new User($username);
                $user->setPassword($encoder->encodePassword($user, $password));
                $user->setUsername($username);
                $user->setRoles(array("ROLE_USER"));
                try {
                    $entityManager->persist($user);
                    $entityManager->flush();
                } catch (\Exception $e) {
                    //$this->setValidationStatusCode();
                    $apiResponse->setValidationStatusCode();
                    throw new \Exception("The username exists");
                }

                //Reset the value of password
                $user->setPassword("");
                $data = $formatData->objectToArrayNormalize($user);
            }
        } catch (\Exception $e) {
            $data = [
                "message" => $e->getMessage()
            ];
        }

        return $apiResponse->response($data);
    }

    /**
     * JWT login authentication for users
     *
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     *
     * @return JsonResponse
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager)
    {
        return new JsonResponse([
            'token' => $JWTManager->create($user)
        ]);
    }
}
