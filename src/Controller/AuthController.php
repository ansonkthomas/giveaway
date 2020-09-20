<?php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends ApiController
{
    /**
     * Register a user
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder) {
        $entityManager = $this->getDoctrine()->getManager();
        $request = UtilityController::transformJsonBody($request);
        $username = $request->get('username');
        $password = $request->get('password');

        try {
            if(!$request) {
                $this->throwBadRequest();
            }
            //Validate the user properties
            $validate = $this->validateUser($request);
            if (count($validate)) {
                $this->setValidationStatusCode();
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
                    $this->setValidationStatusCode();
                    throw new \Exception("The username exists");
                }

                //Reset the value of password
                $user->setPassword("");
                $data = UtilityController::objctToArrayNormalize($user);
            }
        } catch (\Exception $e) {
            $data = [
                "message" => $e->getMessage()
            ];
        }

        return $this->response($data);
    }

    /**
     * JWT login authentication for users
     *
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     *
     * @return JsonResponse
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager) {
        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }

    /**
     * Validate user parameters
     *
     * @param array $request

     * @return array $validate
     */
    private function validateUser($request) {
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
