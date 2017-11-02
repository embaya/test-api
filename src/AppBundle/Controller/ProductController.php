<?php
/**
 * Created by PhpStorm.
 * User: macmini
 * Date: 02/11/2017
 * Time: 13:18
 */

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Form\ProductType;
use AppBundle\Entity\Product;
use Nelmio\ApiDocBundle\Annotation as Doc;


class ProductController extends FOSRestController
{
    /**
     * @Rest\View(serializerGroups={"product"})
     * @Rest\Get("/products")
     *
     * @Doc\ApiDoc(
     *     resource=true,
     *     section="Products",
     *     description="Get the list of all Products",
     *     statusCodes={
     *         200="Returned when Products found",
     *         404="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getProductsAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')->findAll();

        if (empty($products)) {
            return \FOS\RestBundle\View\View::create(['messsage' => 'No Products'], Response::HTTP_NOT_FOUND);
        }

        return $products;
    }

    /**
     * @Rest\View(serializerGroups={"product"})
     * @Rest\Get("products/{id}")
     *
     * @Doc\ApiDoc(
     *     resource=true,
     *     section="Products",
     *     description="Get one Product.",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The Product id"
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when Products found",
     *         404="Returned when a violation is raised by validation"
     *     }
     * )
     *
     */
    public function getProductAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle:Product')->find($request->get('id'));
        if(empty($product)){
            return \FOS\RestBundle\View\View::create(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return $product;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"product"})
     * @Rest\Post("/products")
     *
     *     @Doc\ApiDoc(
     *     resource=true,
     *     section="Products",
     *     description="Post a Product.",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     },
     *     input= {
     *         "class" = "AppBundle\Form\ProductType",
     *         "name" = ""
     *  }
     * )
     */
    public function postProductsAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->submit($request->request->all()); //Validation des données

        if($form->isValid()){
            $category = $form->get('categories')->getData();
            if($category)
                $product->addCategory($category);
            $em->persist($product);
            $em->flush();
            return $product;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"product"})
     * @Rest\Delete("/products/{id}")
     *
     * @Doc\ApiDoc(
     *     resource=true,
     *     section="Products",
     *     description="Delete a Product.",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The Product id."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when Products found",
     *         404="Returned when a violation is raised by validation"
     *     }
     * )
     *
     */
    public function deleleproductsAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository('AppBundle:Product')->find($request->get('id'));

        if(empty($product)){
            return \FOS\RestBundle\View\View::create(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($product);
        $em->flush();

    }

    /**
     * @Rest\View(serializerGroups={"product"})
     * @Rest\Put("/products/{id}")
     *
     * @Doc\ApiDoc(
     *     resource=true,
     *     section="Products",
     *     description="Update Product.",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The Product id."
     *         }
     *     },
     *     input= {
     *         "class" = "AppBundle\Form\ProductType",
     *         "name" = ""
     *     },
     *     statusCodes={
     *         200="Returned when Products found",
     *         404="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function updateProductAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository('AppBundle:Product')->find($request->get('id'));
        if(empty($product)){
            return \FOS\RestBundle\View\View::create(['messsage' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->submit($request->request->all(), true);
        if($form->IsValid()){
            $category = $form->get('categories')->getData();
            if($category)
                $product->addCategory($category);
            $em->merge($product);
            $em->flush();
            return $product;
        } else {
            return $form;
        }
    }

}