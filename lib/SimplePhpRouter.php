<?php

namespace Murtada;

use RecursiveDirectoryIterator;
use UnexpectedValueException;
use OutOfBoundsException;

/**
 * Simple PHP router for routing pretty URLs and parameters to pages.
 */
class SimplePhpRouter {
  private $rootPath;
  private $rootPathParts;
  private $routes;
  public $activeRoute;
  public $baseUrl;

  /**
   * Constructor
   *
   * @param string $routesPath Path to routes file
   */
  public function __construct($routesPath)
  {
    $this->rootPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', getcwd());
    $this->rootPathParts = explode('/', $this->rootPath);
    $this->routesPath = $routesPath;
    $this->baseUrl = join('/', $this->rootPathParts) . '/';

    $this->defaultMenuOptions = [
      'prefix' => '<li class="item%active-class%"><a class="item__link" href="%link%">',
      'suffix' => '</a></li>',
      'activeClass' => 'item--active',
      'ancestorClass' => 'item--ancestor'
    ];

    $this->routes = $this->loadRoutes($this->routesPath);
    $this->resolve();
  }

  /**
   * Iterate over JSON object to find get routes
   *
   * @param string path Path of routes file to iterate over
   *
   * @throws OutOfBoundsException If path cannot be resolved
   *
   * @return array
   */
  private function loadRoutes($path = null): array
  {
    if (!file_exists($path)) {
      throw new OutOfBoundsException(
        'Provided path for routes file cannot be resolved'
      );
    }

    $routesData = json_decode(file_get_contents($path), true);

    $this->iterateRoutes($routesData);

    return $routesData;
  }

  /**
   * Iterate over a directory to find files
   *
   * @param string path Path of directory to iterate over
   *
   * @throws OutOfBoundsException If route path cannot be resolved
   */
  private function iterateRoutes(&$filesRoutes = null)
  {
    foreach ($filesRoutes as $route) {
      $path = getcwd() . '/' . $route['path'];

      if (!file_exists($path)) {
        throw new OutOfBoundsException(sprintf(
          'Provided path (%1$s) for route "%2$s" cannot be resolved',
          $path,
          is_array($route['route'])
            ? join('", "', $route['route'])
            : $route['route']
        ));

        if (isset($route['children'])) {
          $this->iterateRoutes($route['children']);
        }
      }

      if (is_array($route['route'])) {
        foreach($route['route'] as $routeSyntax) {
          if ($routeSyntax === '' || $routeSyntax === 'home') {
            $filesRoutes['__spr_default'] = [];
            $filesRoutes['__spr_default'] = array_merge($filesRoutes['__spr_default'], $route);
            $filesRoutes['__spr_default']['menu_hidden'] = true;
          }
        }
      } else {
        if ($route['route'] === '' || $routeSyntax === 'home') {
          $filesRoutes['__spr_default'] = [];
          $filesRoutes['__spr_default'] = array_merge($filesRoutes['__spr_default'], $route);
          $filesRoutes['__spr_default']['menu_hidden'] = true;
        }
      }
    }
  }

  /**
   * Resolve a given route to a page
   *
   * @throws UnexpectedValueException If no valid path is provided
   * @throws UnexpectedValueException If provided path does not exist
   * @throws OutOfBoundsException If no path cannot be resolved
   *
   * @param string $path Optional path to be resolved, defaults to current REQUEST_URI
   *
   * @return string
   */
  public function resolve($path = null): array
  {
    if (!isset($path)) {
      $path = rtrim($_SERVER['REQUEST_URI'], '/');
    } else if (!is_string($path)) {
      throw new UnexpectedValueException(
        'Provided path is not of type string'
      );
    } else {
      $path = $this->baseUrl . $path;
    }

    $pathParts = $this->getRelativePath($path);

    $resolved = $this->searchRoute($pathParts, $this->routes, []);

    if (!isset($resolved)) {
      $resolved = [
        'route' => $this->routes['404']
      ];

      // The following exception is for debugging unresolved paths
      // throw new OutOfBoundsException(
      //   'Provided path cannot be resolved'
      // );
    }

    $this->currentRoute = $resolved;
    $this->currentPath = implode('/', $pathParts);

    if (!is_callable(require $this->currentRoute['route']['path'])) {
      throw new UnexpectedValueException(
        sprintf(
          'Resolved page "%1$s" does not have a callable ',
          $this->currentRoute['route']['slug']
        )
      );
    }

    $this->currentPage = (object) (require $this->currentRoute['route']['path'])($this);
    $this->currentPage->title = isset($this->currentPage->title) ? $this->currentPage->title : $resolved['route']['title'];
    $this->currentPage->slug = $resolved['route']['slug'];

    return $resolved;
  }

  /**
   * Require a file and return the output buffer
   *
   * @param string $path Path to the file to require
   *
   * @return any
   */
  private function requireToBuffer($path)
  {
    ob_start();
    require($path);
    return ob_get_clean();
  }

  /**
   * Search routes recursively for a path
   *
   * @param string $pathParts Parts of the path (needles)
   * @param array $routesPart Part of the routes (haystack)
   *
   * @return array
   */
  private function searchRoute($pathParts = null, $routesPart, $routes = null)
  {
    if (!isset($pathParts[0])) {
      // Default route
      return [
        'route' => $routesPart['__spr_default']
      ];
    }

    $path = '';

    if (isset($routes)) {
      foreach($routes as $prevRoute) {
        $path .= $prevRoute->path;
      }
    }

    foreach ($routesPart as $route) {
      if (!isset($route['route'])) {
        return;
      }

      $routeLabels = (array) $route['route'];

      foreach ($routeLabels as $label) {
        $routeParts = explode('/', $label);

        // Found a route
        if ($routeParts[0] == $pathParts[0]) {


          $params = [];
          $path .= '/' . $pathParts[0];

          while (count($routeParts) > 1) {
            if (strpos($routeParts[1], ':') === 0
                && isset($pathParts[1])) {
              $params[substr($routeParts[1], 1)] = (isset($pathParts[1])
                ? $pathParts[1]
                : null);

              $path .= '/' . $pathParts[1];
              array_shift($pathParts);
            }

            array_shift($routeParts);
          }


          if (isset($routes)) {
            $routes[] = new Route($route, $params, $path);
          }

          if (count($pathParts) > 1) {
            array_shift($pathParts);

            if (!isset($route['children'])) {
              return;
            }

            return $this->searchRoute($pathParts, $route['children'], $routes);
          }

          return [
            'route' => $route,
            'routes' => $routes
          ];
        }
      }
    }

    return;
  }

  /**
   * Get the relative path
   *
   * @param string $path Path to be made relative to project root
   *
   * @return array
   */
  private function getRelativePath($path): array
  {
    $pathParts = explode('/', $path);

    foreach($pathParts as $partIndex => $part) {
      if (isset($this->rootPathParts[$partIndex])
          && $part === $this->rootPathParts[$partIndex]) {
        array_splice($pathParts, 0, 1);
      }
    }

    return $pathParts;
  }

  /**
   * Returns a list of routes from a parent path
   *
   * @param string $path Parent path
   *
   * @return array||null
   */
  public function getRoute($path)
  {
    $pathParts = $this->getRelativePath($path);
    $resolved = $this->searchRoute($pathParts, $this->routes, []);

    return $resolved;
  }

  /**
   * Returns an array of parameters given by the URL
   *
   * @return array||null
   */
  public function getParams($index = null)
  {
    if (!isset($this->currentRoute['routes'])) {
      return [];
    }

    $keys = array_keys($this->currentRoute['routes']);

    $index = $index < 0 || $index === null
              ? end($keys) + $index
              : $index;

    return $this->currentRoute['routes'][$index]->params;
  }

  /**
   * Returns an HTML formatted menu from a provided route
   *
   * @param string $path Children to be used as items from specified route
   *
   * @return string||null
   */
  public function createMenu($route = null, $options = null)
  {
    if (!isset($options)) {
      $options = $this->defaultMenuOptions;
    }

    if (!isset($route) || $route === null) {
      $resolved = $this->routes;
    } else if (isset($route)) {
      $resolved = $this->getRoute($route);
    } else {
      $resolved = $this->getRoute(rtrim($_SERVER['REQUEST_URI'], '/'));
    }

    if (!isset($resolved['route']['children'])) {
      $children = $resolved;
    } else {
      $children = $resolved['route']['children'];
    }

    $childPath = $this->baseUrl;

    if (isset($resolved['routes'])) {
      $index = 0;

      $childPath .= array_reduce($resolved['routes'], function($prevResult, $route) use (&$index) {
        $currentRoutes = (array)$route->route['route'];
        $urlPart = end($currentRoutes);

        if (strlen($urlPart) > 0) {
          $urlPart .= '/';
        }

        $urlPart = $this->mapParams($urlPart, $this->getParams($index));

        $index++;

        return $prevResult . $urlPart;
      });
    }

    // Find the child route
    return array_reduce($children, function($prev, $child) use ($childPath, $options, $children) {
      if (isset($child['menu_hidden']) && $child['menu_hidden']) {
        return $prev;
      }

      $activeClass = ($this->currentRoute['route']['path'] == $child['path']
        ? ' ' . $options['activeClass']
        : null);

      // Check if item is an ancestor of the current route
      if (!isset($activeClass) && isset($this->currentRoute['routes'])) {
        foreach ($this->currentRoute['routes'] as $cRoute) {
          if ($cRoute->route === $child) {
            $activeClass = isset($options['ancestorClass'])
              ? ' ' . $options['ancestorClass']
              : ' ' . $this->defaultMenuOptions['ancestorClass'];
            break;
          }
        }
      }

      $menuRoute = (isset($child['menu_route']) ? $child['menu_route'] : ((array) $child['route'])[0]);

      $menuItemParams = (isset($options['params'][$child['slug']]) ? $options['params'][$child['slug']] : []);
      $mappedMenuRoute = $this->mapParams($menuRoute, $menuItemParams);

      $prefix = str_replace('%active-class%', $activeClass, $options['prefix']);
      $prefix = str_replace('%link%', $childPath . $mappedMenuRoute, $prefix);

      return $prev . $prefix . $child['title'] . $options['suffix'];
    });
  }

  /**
   * Returns a params mapped path
   *
   * @param string $path A path to be mapped with available params
   *
   * @param array $params Array with key value params to me mapped to the path string
   *
   * @return string
   */
  private function mapParams($path, $params)
  {
    $pathParts = explode('/', rtrim($path, '/'));

    $mappedPathParts = '';

    forEach($pathParts as &$part) {
      if (strpos($part, ':') === 0) {
        $paramName = substr($part, 1);

        if (isset($params[$paramName])) {
          $mappedPathParts .= $params[$paramName] . '/';
          continue;
        }
      } else {
        $mappedPathParts .= $part . '/';
      }
    }

    return $mappedPathParts;
  }
}
