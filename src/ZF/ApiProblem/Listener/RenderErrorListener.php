<?php
/**
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

namespace ZF\ApiProblem\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ModelInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\Exception\ProblemExceptionInterface;
use ZF\ApiProblem\View\ApiProblemModel;

/**
 * RenderErrorListener
 *
 * Provides a listener on the render.error event, at high priority.
 */
class RenderErrorListener extends AbstractListenerAggregate
{
    /**
     * @var bool
     */
    protected $displayExceptions = false;

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'onRenderError'), 100);
    }

    /**
     * @param  bool $flag 
     * @return self
     */
    public function setDisplayExceptions($flag)
    {
        $this->displayExceptions = (bool) $flag;
        return $this;
    }

    /**
     * Handle rendering errors
     *
     * Rendering errors are usually due to trying to render a template in 
     * the PhpRenderer, when we have no templates.
     *
     * As such, report as an unacceptable response.
     *
     * @param  MvcEvent $e 
     */
    public function onRenderError(MvcEvent $e)
    {
        $response    = $e->getResponse();
        $status      = 406;
        $title       = 'Not Acceptable';
        $describedBy = 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html';
        $detail      = 'Your request could not be resolved to an acceptable representation.';
        $details     = false;

        $exception   = $e->getParam('exception');
        if ($exception instanceof \Exception
            && !$exception instanceof \Zend\View\Exception\ExceptionInterface
        ) {
            $code = $exception->getCode();
            if ($code >= 100 && $code <= 600) {
                $status = $code;
            } else {
                $status = 500;
            }
            $title   = 'Unexpected error';
            $detail  = $exception->getMessage();
            $details = array(
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
                'trace'   => $exception->getTraceAsString(),
            );
        }

        $payload = array(
            'httpStatus'  => $status,
            'title'       => $title,
            'describedBy' => $describedBy,
            'detail'      => $detail,
        );
        if ($details && $this->displayExceptions) {
            $payload['details'] = $details;
        }

        $response->getHeaders()->addHeaderLine('content-type', 'application/api-problem+json');
        $response->setStatusCode($status);
        $response->setContent(json_encode($payload));

        $e->stopPropagation();
    }
}