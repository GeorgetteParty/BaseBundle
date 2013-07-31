<?php

namespace GeorgetteParty\BaseBundle\Listener;

use GeorgetteParty\BaseBundle\Utils\ClassGuesser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Sensio\Bundle\FrameworkExtraBundle\EventListener\TemplateListener;
use Symfony\Component\Templating\TemplateReference;

/**
 * Class ParametersTemplateListener
 * Add globals parameters to all twig templates
 * @package GeorgetteParty\BaseBundle\Listener
 */
class ParametersTemplateListener extends TemplateListener
{
    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event
     * @return array|null
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $parameters = $event->getControllerResult();
        $templating = $this->container->get('templating');

        if (null === $parameters) {
            if (!$vars = $request->attributes->get('_template_vars')) {
                if (!$vars = $request->attributes->get('_template_default_vars')) {
                    return null;
                }
            }
            $parameters = array();

            foreach ($vars as $var) {
                $parameters[$var] = $request->attributes->get($var);
            }
        }
        if (!is_array($parameters)) {
            return $parameters;
        }
        if (!$template = $request->attributes->get('_template')) {
            return $parameters;
        }
        // TODO add a trigger in conf
        // dynamically add widgets into template
        $layout = $this->getLayout($request->attributes->get('_template'));
        // you can add here your own twig parameters
        $parameters = array_merge($parameters, array('mainTemplate' => $layout));

        if (!$request->attributes->get('_template_streamable')) {
            $event->setResponse($templating->renderResponse($template, $parameters));
        }
        else {
            $callback = function () use ($templating, $template, $parameters) {
                return $templating->stream($template, $parameters);
            };
            $event->setResponse(new StreamedResponse($callback));
        }
        return null;
    }

    /**
     * Return main layout name
     * @param \Symfony\Component\Templating\TemplateReference $templateReference
     * @return string
     */
    protected function getLayout(TemplateReference $templateReference)
    {
        $bundle = $templateReference->get('bundle');
        $template = '%s:Layout:content.layout.html.twig';

        return sprintf($template, $bundle);
    }
}