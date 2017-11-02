<?php
/**
 * Created by PhpStorm.
 * User: macmini
 * Date: 02/11/2017
 * Time: 14:07
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Entity\Brand;
use AppBundle\Form\BrandType;
use Nelmio\ApiDocBundle\Annotation as Doc;

class BrandController extends Controller
{

    /**
     * @Rest\View()
     * @Rest\Get("/brands")
     *
     * @Doc\ApiDoc(
     *     resource=true,
     *     section="Brands",
     *     description="Get the list of all Brands",
     *     statusCodes={
     *         200="Returned when Brands found",
     *         404="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getBrandsAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $brands = $em->getRepository('AppBundle:Brand')->findAll();
        if (empty($brands)) {
            return \FOS\RestBundle\View\View::create(['messsage' => 'No Brands'], Response::HTTP_NOT_FOUND);
        }
        return $brands;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/brands/{id}")
     *
     * @Doc\ApiDoc(
     *     resource=true,
     *     section="Brands",
     *     description="Get one Brand.",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The Brand id"
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when Brands found",
     *         404="Returned when a violation is raised by validation"
     *     }
     * )
     *
     *
     */
    public function getBrandAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $brand = $em->getRepository('AppBundle:Brand')->find($request->get('id'));
        if (empty($brand)) {
           return \FOS\RestBundle\View\View::create(['messsage' => 'Brand not found'], Response::HTTP_NOT_FOUND);
        }

        return $brand;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/brands")
     *
     *     @Doc\ApiDoc(
     *     resource=true,
     *     section="Brands",
     *     description="Post a Brand.",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     },
     *     input= {
     *         "class" = "AppBundle\Form\BrandType",
     *         "name" = ""
     *  }
     * )
     *
     */
    public function postBrandAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        $brand = new Brand();

        $form = $this->createForm(BrandType::class, $brand);
        $form->submit($request->request->all()); //Validation des données

        if($form->isValid()){
            $em->persist($brand);
            $em->flush();
            return $brand;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/brands/{id}")
     *
     * @Doc\ApiDoc(
     *     resource=true,
     *     section="Brands",
     *     description="Delete a Brand.",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The Brand id."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when Brands found",
     *         404="Returned when a violation is raised by validation"
     *     }
     * )
     *
     */
    public function delelebrandsAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        $brand = $em->getRepository('AppBundle:Brand')->find($request->get('id'));
        if (empty($brand)) {
            return \FOS\RestBundle\View\View::create(['messsage' => 'Brand not found'], Response::HTTP_NOT_FOUND);
        }
        $em->remove($brand);
        $em->flush();

    }

    /**
     * @Rest\View()
     * @Rest\Put("/brands/{id}")
     *
     * @Doc\ApiDoc(
     *     resource=true,
     *     section="Brands",
     *     description="Update Brand.",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The Brand id."
     *         }
     *     },
     *     input= {
     *         "class" = "AppBundle\Form\BrandType",
     *         "name" = ""
     *     },
     *     statusCodes={
     *         200="Returned when Brands found",
     *         404="Returned when a violation is raised by validation"
     *     }
     * )
     *
     */
    public function updateBrandAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $brand = $em->getRepository('AppBundle:Brand')->find($request->get('id'));
        if(empty($brand)){
            return \FOS\RestBundle\View\View::create(['messsage' => 'Brand not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(BrandType::class, $brand);
        $form->submit($request->request->all(), true);
        if($form->IsValid()){
            $em->merge($brand);
            $em->flush();
            return $brand;
        } else {
            return $form;
        }
    }
}