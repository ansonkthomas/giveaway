<?php
  namespace App\Controller;

  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
  use Symfony\Component\HttpFoundation\Session\SessionInterface;

  class LuckyController extends ApiController {

      /**
      * @Route("/api/lucky/number", name="app_lucky_number", methods={"GET", "POST"})
      */
      public function number() {
          $number = random_int(0, 100);

          return $this->respondWithSuccess($number);
      }

      /**
      * @Route("/static/number/{number}", name="app_static_number", defaults={"number": 22}, methods={"GET", "POST"}, requirements={"number"="\d+"}, host="giveaway.com")
      */
      public function staticNumber(int $number, Request $request, SessionInterface $session) {
          //throw $this->createNotFoundException("Number does not found");
          $routeName = $request->attributes->get("_route");
          echo $routeName . "\n";

          $routeParams = $request->attributes->get("_route_params");
          print_r($routeParams) . "\n";

          $all = $request->attributes->all();
          print_r($all) . "\n";

          $luckyNumberUrl = $this->generateUrl("app_lucky_number");
          $session->set("foo", "bar");
          $value = $session->get("foo");
          echo "    " . $value;

          //echo $request->server->get("SERVER_NAME"); exit;

          echo $request->headers->get('host'); exit;
          echo $request->headers->get('content-type'); exit;

          return $this->render("static_number.html.twig", [
              "number" => $number,
              "luckyNumberUrl" => $luckyNumberUrl
          ]);
      }
  }


?>
