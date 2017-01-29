<?php

namespace TitansInc\CORS\Resolver;

class ConfigResolver {
    protected $paths;
    protected $defaults;

    public function __construct(array $defaults = [], $paths) {
        $this->defaults = $defaults;
        $this->paths = $paths;
    }

    public function getOptions($request) {

        $uri = $request->getPathInfo() ?: '/';
        
        foreach ($this->paths as $path) {
            
            if (preg_match('{'.$path->path.'}i', $uri)) {
                
                $options = array_merge($this->defaults, $path->toArray([], ['path']));

                // skip if the host is not matching
                if (count($options['hosts']) > 0) {
                    foreach ($options['hosts'] as $hostRegexp) {
                        if (preg_match('{'.$hostRegexp.'}i', $request->getHost())) {
                            return $options;
                        }
                    }
                    continue;
                }

                return $options;
            }
        }
        return $this->defaults;
    }
}