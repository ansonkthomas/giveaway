<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends ApiController
{
    /**
     * Create a product
     *
     * @Route("/api/product/create", name="create_product", methods={"POST"})
     */
    public function createProduct(Request $request) {
        $entityManager = $this->getDoctrine()->getManager();
        try {
            $request = UtilityController::transformJsonBody($request);
            if(!$request || empty($request->get("name")) || empty($request->get("type"))) {
                throw new \Exception;
            }

            $product = new Product();
            $product->setName($request->get("name"));
            $product->setType($request->get("type"));
            $entityManager->persist($product);
            $entityManager->flush();
            $data = UtilityController::objectToJsonSerializer($product);
        } catch (\Exception $e) {
            $data = [
                "status" => 422,
                "error" => "Invalid parameters"
            ];
        }

        return $this->response($data);
    }

    /**
     * Get the product details
     *
     * @Route("/api/product/{id}", name = "show_product", requirements={"number"="\d+"})
     */
    public function showProduct(int $id) {
        $entityManager = $this->getDoctrine()->getManager();
        try {
            $product = $entityManager->getRepository(Product::class)->find($id);
            if (!$product) {
                throw new \Exception;
            }
            $data = UtilityController::objectToJsonSerializer($product);
        } catch (\Exception $e) {
            $data = [
                "status" => 404,
                "error" => "The product does not found"
            ];
        }

        return $this->response($data);
    }

    /**
     * Update the product details
     *
     * @Route("/api/product/update/{id}", name = "update_product", requirements={"number"="\d+"}, methods = {"POST"})
     */
    public function updateProduct(Request $request, int $id) {
        $entityManager = $this->getDoctrine()->getManager();
        $request = UtilityController::transformJsonBody($request);
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
     * Delete a product
     *
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
