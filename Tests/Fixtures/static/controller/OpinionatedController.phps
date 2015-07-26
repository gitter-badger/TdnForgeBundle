<?php

namespace Foo\BarBundle\Controller;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations as Rest;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Foo\BarBundle\Exception\InvalidFormException;
use Foo\BarBundle\Handler\FooHandler;
use Foo\BarBundle\Entity\FooInterface;

/**
 * Class FooController
 * @package Foo\BarBundle\Controller
 */
class FooController extends FOSRestController
{
    /**
     * Get a Foo
     *
     * @ApiDoc(
     *   section = "Foo",
     *   description = "Get a Foo.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="Foo identifier."
     *     }
     *   },
     *   output="Foo\BarBundle\Entity\Foo",
     *   statusCodes={
     *     200 = "Foo.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @throws NotFoundHttpException When the resource is not found.
     *
     * @return Response
     */
    public function getFooAction($id)
    {
        return $this->getOr404($id);
    }

    /**
     * Get all Foo types.
     *
     * @ApiDoc(
     *   section = "Foo",
     *   description = "Get all Foo.",
     *   resource = true,
     *   output="Foo\BarBundle\Entity\Foo",
     *   statusCodes = {
     *     200 = "List of all Foo",
     *     204 = "No content. Nothing to list."
     *   }
     * )
     *
     * @Rest\QueryParam(
     *   name="offset",
     *   requirements="\d+",
     *   nullable=true,
     *   description="Offset from which to start. Defaults to 0."
     * )
     * @Rest\QueryParam(
     *   name="limit",
     *   requirements="\d+",
     *   default="20",
     *   description="Max number of results."
     * )
     * @Rest\QueryParam(
     *   name="order_by",
     *   nullable=true,
     *   array=true,
     *   description="Order by fields. Must be an array ie. &order_by[name]=ASC&order_by[description]=DESC"
     * )
     * @Rest\QueryParam(
     *   name="filters",
     *   nullable=true,
     *   array=true,
     *   description="Filter by fields. Must be an array ie. &filters[id]=3"
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Response
     */
    public function getFoosAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $orderBy = $paramFetcher->get('order_by');
        $criteria = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : [];
        $criteria = array_map(function ($item) {
            $item = $item == 'null' ? null : $item;
            $item = $item == 'false' ? false : $item;
            $item = $item == 'true' ? true : $item;

            return $item;
        }, $criteria);

        $result = $this->getFooHandler()->findFoosBy(
            $criteria,
            $orderBy,
            $limit,
            $offset
        );

        //If there are no matches return an empty array
        return $result ?: new ArrayCollection([]);
    }

    /**
     * Create a Foo.
     *
     * @ApiDoc(
     *   section = "Foo",
     *   description = "Create a Foo.",
     *   resource = true,
     *   input="Foo\BarBundle\Form\Type\FooType",
     *   output="Foo\BarBundle\Entity\Foo",
     *   statusCodes={
     *     201 = "Created Foo.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(statusCode=201, serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postFooAction(Request $request)
    {
        try {
            return $this->handleView(
                $this->view(
                    $this->getFooHandler()->post($this->getPayload($request)),
                    null,
                    [
                        'Location' => $this->generateUrl(
                            'get_foos',
                            ['id' => $foo->getId()],
                            true
                        )
                    ]
                )
            );
        } catch (InvalidFormException $exception) {
            return $this->handleView(
                $this->view(
                    $exception->getErrors(),
                    $exception->getStatusCode(),
                    $exception->getHeaders()
                )
            );
        }
    }

    /**
     * Update a Foo.
     *
     * @ApiDoc(
     *   section = "Foo",
     *   description = "Update a Foo entity.",
     *   resource = true,
     *   input="Foo\BarBundle\Form\Type\FooType",
     *   output="Foo\BarBundle\Entity\Foo",
     *   statusCodes={
     *     200 = "Updated Foo.",
     *     201 = "Created Foo.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(statusCode=200, serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @throws NotFoundHttpException When the resource is not found.
     *
     * @return Response
     */
    public function putFooAction(Request $request, $id)
    {
        try {
            return $this->getFooHandler()->put(
                $this->getOr404($id),
                $this->getPayload($request)
            );
        } catch (InvalidFormException $exception) {
            return $this->handleView(
                $this->view(
                    $exception->getErrors(),
                    $exception->getStatusCode(),
                    $exception->getHeaders()
                )
            );
        }
    }

    /**
     * Delete a Foo.
     *
     * @ApiDoc(
     *   section = "Foo",
     *   description = "Delete a Foo entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "Foo identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Foo.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     *
     * @throws NotFoundHttpException When the resource is not found.
     * @throws \Exception When an error occurs when deleting resource.
     *
     * @return Response
     */
    public function deleteFooAction($id)
    {
        $foo = $this->getOr404($id);

        try {
            $this->getFooHandler()->deleteFoo($foo);

            return $this->handleView($this->view(null, Codes::HTTP_NO_CONTENT));
        } catch (\Exception $exception) {
            throw $this->createException(sprintf("Error deleting resource '%s'", $id));
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     *
     * @throws NotFoundHttpException When the resource is not found.
     *
     * @return FooInterface $foo
     */
    protected function getOr404($id)
    {
        if (null === $foo = $this->getFooHandler()->findFooBy(['id' => $id])) {
            throw $this->createNotFoundException(sprintf("The resource '%s' was not found.", $id));
        }

        return $foo;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPayload(Request $request)
    {
        $data = $request->request->get('foo');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return FooHandler
     */
    protected function getFooHandler()
    {
        return $this->container->get('foo_bar.handler.foo_handler');
    }
}
