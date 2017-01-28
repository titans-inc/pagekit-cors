<?php

namespace TitansInc\CORS\Listener;

use Pagekit\Event\Event;
use Pagekit\Event\EventSubscriberInterface;

class CORSListener implements EventSubscriberInterface {

    /**
     * Simple headers as defined in the spec should always be accepted
     */
    protected static $simpleHeaders = [
        'accept',
        'accept-language',
        'content-language',
        'origin',
    ];

    /**
     * {@inheritdoc}
     */
    public function subscribe() {
        return [
            'request' => ['onRequest', 250],
        ];
    }

    public function __construct($app) {
        $this->dispatcher = $app['events'];
        $this->module = $app['module']('CORS');
    }

    /**
     * @param Event $event
     * @param object $request Symfony HTTPFoundation Request
     */
    public function onRequest(Event $event, $request) {
        // Don't use CORS if not a master request
        if(!$event->isMasterRequest()) {
            return;
        }

        $options = $this->module->config;

        //TODO Implement Configuration Resolver
        /** if (!$options = $this->configurationResolver($request, $paths)) {
            return;
        } */

        if (!empty($options['forced_allow_origin_value'])) {
            $this->dispatcher->on('response', [$this, 'forceAccessControlAllowOriginHeader'], -1);
        }
        
        // skip if not a CORS request
        if (!$request->headers->has('Origin') || $request->headers->get('Origin') == $request->getSchemeAndHttpHost()) {
            return;
        }

        if ('OPTIONS' === $request->getMethod()) {
            //TODO Send Preflight Response
            return;
        }
        
        if (!$this->checkOrigin($request, $options)) {
            return;
        }

        $this->dispatcher->on('response', [$this, 'onResponse'], 0);

    }

    public function onResponse($event, $request, $response)
    {
        // Don't use CORS if not a master request
        if(!$event->isMasterRequest()) {
            return;
        }

        $options = $this->module->config; //TODO Implement Configuration Resolver
        
        // add CORS response headers
        $response->headers->set('Access-Control-Allow-Origin', $request->headers->get('Origin'));
        if ($options['allow_credentials']) {
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }
        if ($options['expose_headers']) {
            $response->headers->set('Access-Control-Expose-Headers', strtolower(implode(', ', $options['expose_headers'])));
        }
    }


    public function forceAccessControlAllowOriginHeader($event, $request, $response) {
        $options = $this->module->config;
        $response->headers->set('Access-Control-Allow-Origin', $options['forced_allow_origin_value']);
    }

    protected function checkOrigin($request, array $options)
    {
        // check origin
        $origin = $request->headers->get('Origin');
        if ($options['allow_origin'] === true) return true;
        if ($options['origin_regex'] === true) {
            // origin regex matching
            foreach($options['allow_origin'] as $originRegexp) {
                if (preg_match('{'.$originRegexp.'}i', $origin)) {
                    return true;
                }
            }
        } else {
            // old origin matching
            if (in_array($origin, $options['allow_origin'])) {
                return true;
            }
        }
        return false;
    }
}