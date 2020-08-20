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
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder) {
        $em = $this->getDoctrine()->getManager();
        $request = UtilityController::transformJsonBody($request);
        $username = $request->get('username');
        $password = $request->get('password');

        if (empty($username) || empty($password)) {
            return $this->respondValidationError("Invalid Username or Password");
        }

        $user = new User($username);
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setUsername($username);
        $user->setRoles(array("ROLE_USER"));

        try {
            $em->persist($user);
            $em->flush();
        } catch (\Exception $e) {
            return $this->respondValidationError("The username exists");
        }

        return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getUsername()));
    }

    /**
     * JWT login authentication for users
     *
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager) {
        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }
}
