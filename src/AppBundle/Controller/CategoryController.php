<?php
/**
 * Created by PhpStorm.
 * User: macmini
 * Date: 02/11/2017
 * Time: 14:08
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;

class CategoryController extends Controller
{
    /**
     * @Rest\View(serializerGroups={"category"})
     * @Rest\Get("/categories")
     */
    public function getCategorieAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('AppBundle:Category')->findAll();
        if (empty($categories)) {
            return \FOS\RestBundle\View\View::create(['messsage' => 'No Categories'], Response::HTTP_NOT_FOUND);
        }
        return $categories;
    }

    /**
     * @Rest\View(serializerGroups={"category"})
     * @Rest\Get("/categories/{id}")
     */
    public function getCategoryAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('AppBundle:Category')->find($request->get('id'));
        if (empty($category)) {
            return \FOS\RestBundle\View\View::create(['messsage' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        return $category;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"category"})
     * @Rest\Post("/categories")
     */
    public function postCategoryAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->submit($request->request->all()); //Validation des données

        if($form->isValid()){
            $em->persist($category);
            $em->flush();
            return $category;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"category"})
     * @Rest\Delete("/categories/{id}")
     */
    public function deleleCategoryAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('AppBundle:Category')->find($request->get('id'));
        if (empty($category)) {
            return \FOS\RestBundle\View\View::create(['messsage' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }
        $em->remove($category);
        $em->flush();

    }

    /**
     * @Rest\View(serializerGroups={"category"})
     * @Rest\Put("/categories/{id}")
     */
    public function updateCategoryAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('AppBundle:Category')->find($request->get('id'));
        if(empty($category)){
            return \FOS\RestBundle\View\View::create(['messsage' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->submit($request->request->all(), true);
        if($form->IsValid()){
            $em->merge($category);
            $em->flush();
            return $category;
        } else {
            return $form;
        }
    }
}