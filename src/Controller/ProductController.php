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
     * @Route("/products", name="create_product", methods={"POST"})
     */
    public function createProduct(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $request = UtilityController::transformJsonBody($request);
        try {
            if (!$request) {
                $this->throwBadRequest();
            }
            //Validate the product properties
            $validate = $this->validateProduct($request);
            if (count($validate)) {
                $this->setValidationStatusCode();
                $data = [
                    "message" => $validate
                ];
            } else {
                //Create an instance of Product entity
                $product = new Product();
                $product->setName($request->get("name"));
                $product->setType($request->get("type"));
                $entityManager->persist($product);
                $entityManager->flush();
                $data = UtilityController::objctToArrayNormalize($product);
            }
        } catch (\Exception $e) {
            $data = [
                "message" => $e->getMessage()
            ];
        }

        return $this->response($data);
    }

    /**
     * Get the product details
     *
     * @Route("/products/{id}", name = "show_product", requirements={"number"="\d+"}, methods={"GET"})
     */
    public function showProduct(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        try {
            $product = $entityManager->getRepository(Product::class)->find($id);
            if (!$product) {
                $this->throwResourceNotFound("The product does not exists");
            }
            $data = UtilityController::objctToArrayNormalize($product);
        } catch (\Exception $e) {
            $data = [
                "message" => $e->getMessage()
            ];
        }

        return $this->response($data);
    }

    /**
     * Update the product details
     *
     * @Route("/products/{id}", name = "update_product", requirements={"number"="\d+"}, methods = {"PUT"})
     */
    public function updateProduct(Request $request, int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $request = UtilityController::transformJsonBody($request);
        try {
            if (!$request) {
                $this->throwBadRequest();
            }
            $product = $entityManager->getRepository(Product::class)->find($id);
            if (!$product) {
                $this->throwResourceNotFound("The product does not exists");
            }

            //Validate the product properties
            $validate = $this->validateProduct($request);
            if (count($validate)) {
                $this->setValidationStatusCode();
                $data = [
                    "message" => $validate
                ];
            } else {
                //Update the product details
                $product->setName($request->get("name"));
                $product->setType($request->get("type"));
                $entityManager->flush();
                $data = UtilityController::objctToArrayNormalize($product);
            }
        } catch (\Exception $e) {
            $data = [
                "message" => $e->getMessage()
            ];
        }

        return $this->response($data);
    }

    /**
     * Delete a product
     *
     * @Route("/products/{id}", name = "delete_product", requirements={"number"="\d+"}, methods = {"DELETE"})
     */
    public function deleteProduct(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        try {
            $product = $entityManager->getRepository(Product::class)->find($id);
            if (!$product) {
               $this->throwResourceNotFound("The product does not exists");
            }
            $entityManager->remove($product);
            $entityManager->flush();
            $data = [
                "message" => "The product has been deleted"
            ];
        } catch (\Exception $e) {
            $data = [
                "message" => $e->getMessage()
            ];
        }

        return $this->response($data);
    }

    /**
     * Validate product parameters
     *
     * @param Request $request
     *
     * @return array $validate
     */
    private function validateProduct($request)
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
}
