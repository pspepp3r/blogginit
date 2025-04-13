<?php

declare(strict_types=1);

namespace Src\Controllers;

use Slim\Views\Twig;
use Src\Services\BlogService;
use Psr\Http\Message\ResponseInterface as Response;


class BlogController
{
    public function __construct(
        private readonly BlogService $blogService,
        private readonly Twig $twig
    ) {
    }

    public function renderReports(Response $response, array $args): Response
    {
        $user = $this->twig->getEnvironment()->getGlobals()['user'];

        $args = [
            'totalBlogPosts' => $this->blogService->totalBlogs($user),
            'totalBlogTicks' => $this->blogService->totalTicks($user),
            'totalBlogViews' => $this->blogService->totalViews($user),
            'averageBlogViews' => $this->blogService->averageViews($user),

            'blogs' => $this->blogService->allBlogs($user)
        ];

        return $this->twig->render($response, 'app/reports.twig', $args);
    }
}
