<?php

namespace TitansInc\CORS\Listener;

use Symfony\Component\HttpFoundation\Response;
use Pagekit\Event\Event;
use Pagekit\Event\EventSubscriberInterface;
use TitansInc\CORS\Resolver\ConfigResolver;
use TitansInc\CORS\Model\Path;

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
        $this->module = $app['module']('pagekit-cors');
        $this->resolver = null;
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

        $this->resolver = new ConfigResolver($this->module->config, Path::where(['status' => true])->get());
        if (!$options = $this->resolver->getOptions($request)) {
            return;
        }

        if (!empty($options['forced_allow_origin_value'])) {
            $this->dispatcher->on('response', [$this, 'forceAccessControlAllowOriginHeader'], -1);
        }
        
        // skip if not a CORS request
        if (!$request->headers->has('Origin') || $request->headers->get('Origin') == $request->getSchemeAndHttpHost()) {
            return;
        }

        if ('OPTIONS' === $request->getMethod()) {
            $event->setResponse($this->getPreflightResponse($request, $options));

            return;
        }
        
        if (!$this->checkOrigin($request, $options)) {
            return;
        }

        $this->dispatcher->on('response', [$this, 'onResponse'], 0);

    }

    public function onResponse($event, $request, $response) {
        // Don't use CORS if not a master request
        if(!$event->isMasterRequest()) {
            return;
        }

        if (!$options = $this->resolver->getOptions($request)) {
            return;
        }
        
        // add CORS response headers
        $response->headers->set('Access-Control-Allow-Origin', $request->headers->get('Origin'));
        if ($options['allow_credentials']) {
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }
        if ($options['expose_headers']) {
            $response->headers->set('Access-Control-Expose-Headers', strtolower(implode(', ', $options['expose_headers'])));
        }
    }

    protected function getPreflightResponse($request, $options)
    {
        $response = new Response();
        if ($options['allow_credentials']) {
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }
        if ($options['allow_methods']) {
            $response->headers->set('Access-Control-Allow-Methods', implode(', ', $options['allow_methods']));
        }
        if ($options['allow_headers']) {
            $headers = $options['allow_headers'] === true
                ? $request->headers->get('Access-Control-Request-Headers')
                : implode(', ', $options['allow_headers']);
            if ($headers) {
                $response->headers->set('Access-Control-Allow-Headers', $headers);
            }
        }
        if ($options['max_age']) {
            $response->headers->set('Access-Control-Max-Age', $options['max_age']);
        }
        if (!$this->checkOrigin($request, $options)) {
            $response->headers->set('Access-Control-Allow-Origin', 'null');
            return $response;
        }
        $response->headers->set('Access-Control-Allow-Origin', $request->headers->get('Origin'));
        // check request method
        if (!in_array(strtoupper($request->headers->get('Access-Control-Request-Method')), $options['allow_methods'], true)) {
            $response->setStatusCode(405);
            return $response;
        }
        /**
         * We have to allow the header in the case-set as we received it by the client.
         * Firefox f.e. sends the LINK method as "Link", and we have to allow it like this or the browser will deny the
         * request.
         */
        if (!in_array($request->headers->get('Access-Control-Request-Method'), $options['allow_methods'], true)) {
            $options['allow_methods'][] = $request->headers->get('Access-Control-Request-Method');
            $response->headers->set('Access-Control-Allow-Methods', implode(', ', $options['allow_methods']));
        }
        // check request headers
        $headers = $request->headers->get('Access-Control-Request-Headers');
        if ($options['allow_headers'] !== true && $headers) {
            $headers = trim(strtolower($headers));
            foreach (preg_split('{, *}', $headers) as $header) {
                if (in_array($header, self::$simpleHeaders, true)) {
                    continue;
                }
                if (!in_array($header, $options['allow_headers'], true)) {
                    $response->setStatusCode(400);
                    $response->setContent('Unauthorized header '.$header);
                    break;
                }
            }
        }
        return $response;
    }


    public function forceAccessControlAllowOriginHeader($event, $request, $response) {
        if (!$options = $this->resolver->getOptions($request)) {
            return;
        }
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