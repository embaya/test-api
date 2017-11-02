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

class ProductController extends FOSRestController
{
    /**
     * @Rest\View(serializerGroups={"product"})
     * @Rest\Get("/products")
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
     * @Rest\Get("products/{product_id}")
     */
    public function getProductAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle:Product')->find($request->get('product_id'));
        if(empty($product)){
            return \FOS\RestBundle\View\View::create(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return $product;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"product"})
     * @Rest\Post("/products")
     */
    public function postProductsAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->submit($request->request->all()); //Validation des données

        if($form->isValid()){
            $category = $form->get('categories')->getData();
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
            $product->addCategory($category);
            $em->merge($product);
            $em->flush();
            return $product;
        } else {
            return $form;
        }
    }

}