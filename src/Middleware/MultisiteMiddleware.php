<?php

namespace Drupal\multisite\Middleware;

use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Template\TwigEnvironment;
use Drupal\multisite\Repository\SiteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class MultisiteMiddleware implements HttpKernelInterface {

    /**
     * The decorated kernel.
     *
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    protected $httpKernel;

    /**
     * The ban IP manager.
     *
     * @var \Drupal\multisite\Manager\MultisiteManagerInterface
     */
    protected $repo;

    /**
     * Constructs a BanMiddleware object.
     *
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $http_kernel
     *   The decorated kernel.
     * @param SiteRepository $repo
     *   The site config repository.
     */
    public function __construct(HttpKernelInterface $http_kernel, SiteRepository $repo) {
        $this->httpKernel = $http_kernel;
        $this->repo = $repo;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = TRUE) {


        if(!$this->isAdminPath($request) && !$this->repo->findByDomain($request->getHost())) {

            $templatePath = drupal_get_path('module', 'multisite') . '/templates/domain_configured.html';
            $markup = file_get_contents($templatePath);

            return new Response($markup, 404);
        }

        return $this->httpKernel->handle($request, $type, $catch);
    }

    /**
     * Check if current path is admin path.
     * @param $request
     * @return bool
     */
    private function isAdminPath(Request $request)
    {


        // support for localhost calls
        if('127.0.0.1' === $request->getClientIp()) {
            return true;
        }

        $path = $request->getPathInfo();

        $adminPaths = [
            '/admin',
            '/user',
            '/batch',
        ];

        foreach ($adminPaths as $adminPath) {

            if(strpos($path, $adminPath) === 0) {
                return true;
            }
        }

        return false;
    }

}