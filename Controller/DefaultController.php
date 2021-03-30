<?php
namespace Treetop1500\EasyadminDragndropSortBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use ReflectionClass;

/**
 * Class DefaultController
 * @package Treetop1500\EasyadminDragndropSortBundle\Controller
 * @author http://github.com/treetop1500
 */
class DefaultController extends AbstractController
{
    /**
     * Resorts an item using it's doctrine sortable property
     *
     * @Route("/sort/{entityClass}", name="easyadmin_dragndrop_sort_sort")
     * @param String $entityClass
     * @throws NotFoundHttpException
     * @throws \ReflectionException
     * @return JsonResponse
     *
     */
    public function sortAction($entityClass):JsonResponse
    {
        $entityClassNameArray = explode('\\', $entityClass);
        $entityClassName = end($entityClassNameArray);
        try {
            $rc = new ReflectionClass($entityClass);
        } catch (\ReflectionException $error) {
            throw new \ReflectionException('The class name '. $entityClass .'  cannot be reflected.');
        }

        $em = $this->getDoctrine()->getManager();
        $datas = json_decode(file_get_contents("php://input"), true) ?: [];
        foreach ($datas as $data)
        {
            $info = explode(',',$data);
            $e = $em->getRepository($rc->getName())->find($info[0]);
            if (is_null($e)) {
                throw new NotFoundHttpException('The entity was not found');
            }
            $e->setPosition($info[1]);
            $em->persist($e);
            $em->flush();
        }
        return $this->json(['success'=>true]);
    }

}
