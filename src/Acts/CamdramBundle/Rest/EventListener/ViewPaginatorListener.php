<?php
namespace Acts\CamdramBundle\Rest\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\Routing\RouterInterface;

use FOS\RestBundle\View\View;
use Pagerfanta\Pagerfanta;

use Acts\CamdramBundle\Rest\PaginatedCollection;
use Acts\CamdramBundle\Rest\ResponseQueryParams;
use Acts\CamdramBundle\Rest\ResponseUrls;

use Acts\DiaryBundle\Diary\Diary;
use Acts\DiaryBundle\Diary\Renderer\HtmlRenderer;


class ViewPaginatorListener
{

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Acts\DiaryBundle\Diary\Renderer\HtmlRenderer
     */
    private $diary_renderer;

    public function __construct(RouterInterface $router, HtmlRenderer $diary_renderer)
    {
        $this->router = $router;
        $this->diary_renderer = $diary_renderer;
    }

    /**
     * Detects a paginator returned at part of a rest view and convert it into a PaginatorCollection
     *
     * @param GetResponseForControllerResultEvent $event A GetResponseForControllerResultEvent instance
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $configuration = $request->attributes->get('_view');

        /** @var $view \FOS\RestBundle\View\View  */
        $view = $event->getControllerResult();
        if ($view instanceof View) {
            if ($view->getData() instanceOf Pagerfanta) {
                $paginator = $view->getData();

                if (!$request->get('page')) {
                    $request->query->set('page', 1);
                }
                if (!$request->get('limit')) {
                    $request->query->set('limit', ($request->getRequestFormat() == 'html') ? 10 : 50);
                }

                $paginator->setMaxPerPage($request->get('limit'));
                $paginator->setCurrentPage($request->get('page'));

                $url = $this->router->generate($request->get('_route'), array('_format' => $request->getRequestFormat('')), true);

                $view->setData(new PaginatedCollection($paginator, $request, $url));
            }
        }
        elseif ($view instanceof Diary) {
            $response = $this->diary_renderer->render($view);
            $response->setSharedMaxAge(60);
            $event->setResponse($response);
        }
    }
}
