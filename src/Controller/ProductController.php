<?php

namespace App\Controller;

use App\Entity\Product;
use App\Utils\FormatData;
use App\Utils\Validation;
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
    public function createProduct(Request $request, FormatData $formatData, Validation $validation)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $request = $formatData->transformJsonBody($request);
        try {
            if (!$request) {
                $this->throwBadRequest();
            }
            //Validate the product properties
            $validate = $validation->validateProduct($request);
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
                $data = $formatData->objectToArrayNormalize($product);
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
    public function showProduct(FormatData $formatData, int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        try {
            $product = $entityManager->getRepository(Product::class)->find($id);
            if (!$product) {
                $this->throwResourceNotFound("The product does not exists");
            }
            $data = $formatData->objectToArrayNormalize($product);
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
    public function updateProduct(Request $request, FormatData $formatData, Validation $validation, int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $request = $formatData->transformJsonBody($request);
        try {
            if (!$request) {
                $this->throwBadRequest();
            }
            $product = $entityManager->getRepository(Product::class)->find($id);
            if (!$product) {
                $this->throwResourceNotFound("The product does not exists");
            }

            //Validate the product properties
            $validate = $validation->validateProduct($request);
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
                $data = $formatData->objectToArrayNormalize($product);
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
}
