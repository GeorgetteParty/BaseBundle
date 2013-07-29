<?php

namespace GeorgetteParty\BaseBundle\Listener;

use GeorgetteParty\BaseBundle\Utils\ClassGuesser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Sensio\Bundle\FrameworkExtraBundle\EventListener\TemplateListener;

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
        $mainTemplate = $this->getMainTemplateName();
        // you can add here your own twig parameters
        $parameters = array_merge($parameters, array('mainTemplate' => $mainTemplate));

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
     * @return string
     */
    protected function getMainTemplateName()
    {
        $guesser = new ClassGuesser($this);
        $bundle = $guesser->getBundle();
        $template = '%s:Layout:content.layout.html.twig';

        return sprintf($template, $bundle);
    }

}