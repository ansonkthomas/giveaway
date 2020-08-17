<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;





class ProductController extends ApiController
{
    /**
     * @Route("/api/product/create", name="create_product", methods={"POST"})
     */
    public function createProduct(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        try {
            $request = $this->transformJsonBody($request);
            if(!$request || empty($request->get("name")) || empty($request->get("type"))) {
                throw new \Exception;
            }

            $product = new Product();
            $product->setName($request->get("name"));
            $product->setType($request->get("type"));
            $entityManager->persist($product);
            $entityManager->flush();

            $encoders = [new JsonEncoder()];
            $normalizers = [new ObjectNormalizer()];
            $serializer = new Serializer($normalizers, $encoders);
            $data = $serializer->serialize($product, 'json');
        } catch (\Exception $e) {
            $data = [
              "status" => 422,
              "error" => "Invalid parameters"
            ];
        }
        return $this->response($data);
    }

    public function show() {
        return $this->respondNotFound("The route does not exists");
    }

    /**
   * @Route("/api/product/{id}", name = "show_product", requirements={"number"="\d+"})
   */
   public function showProduct(int $id)
   {
      $entityManager = $this->getDoctrine()->getManager();
      try {
          $product = $entityManager->getRepository(Product::class)->find($id);
          if (!$product) {
              throw new \Exception;
          }
      } catch (\Exception $e) {
          return $this->respondNotFound("The product does not found");
      }

      $encoders = [new JsonEncoder()];
      $normalizers = [new ObjectNormalizer()];
      $serializer = new Serializer($normalizers, $encoders);
      $jsonContent = $serializer->serialize($product, 'json');

      return $this->response($jsonContent);
   }

   /**
   * @Route("/api/product/update/{id}", name = "update_product", requirements={"number"="\d+"}, methods = {"POST"})
   */
   public function updateProduct(Request $request, int $id)
   {
       $entityManager = $this->getDoctrine()->getManager();
       $request = $this->transformJsonBody($request);
       try {
           $product = $entityManager->getRepository(Product::class)->find($id);
           if (!$product) {
             return $this->respondNotFound("The product does not found");
           }

           if(empty($request->get("name")) || empty($request->get("type"))) {
               throw new \Exception;
           }

           $product->setName($request->get("name"));
           $product->setType($request->get("type"));
           $entityManager->flush();

           $data = [
             "status" => 200,
             "success" => "The product has been updated"
           ];
       } catch (\Exception $e) {
           $data = [
             "status" => 422,
             "error" => "Invalid parameters"
           ];
       }
       return $this->response($data);
   }

   /**
   * @Route("/api/product/delete/{id}", name = "delete_product", requirements={"number"="\d+"}, methods = {"DELETE"})
   */
   public function deleteProduct(int $id) {
       $entityManager = $this->getDoctrine()->getManager();
       try {
          $product = $entityManager->getRepository(Product::class)->find($id);
          if (!$product) {
              throw new \Exception();
          }
          $entityManager->remove($product);
          $entityManager->flush();
          $data = [
            "status" => 200,
            "success" => "The product has been deleted"
          ];
       } catch (\Exception $e) {
          $data = [
            "status" => 200,
            "error" => "The product does not found"
          ];
       }
       return $this->response($data);
   }
}
