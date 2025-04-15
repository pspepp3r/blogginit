<?php

declare(strict_types=1);

namespace Src\Controllers;

use Slim\Views\Twig;
use Src\Services\BlogService;
use Psr\Http\Message\ResponseInterface as Response;


class BlogsController
{
    public function __construct(
        private readonly BlogService $blogService,
        private readonly Twig $twig
    ) {
    }

    public function renderBlogs(Response $response, array $args): Response
    {

        $user = $this->twig->getEnvironment()->getGlobals()['user'];

        $args = [
            'blogs' => $this->blogService->allBlogs($user)
        ];

        return $this->twig->render($response, 'app/blogs.twig', $args);
    }

    public function renderCreateBlog(Response $response, array $args): Response
    {
        return $this->twig->render($response, 'app/create.twig', $args);
    }

    public function renderBlog(Response $response, array $args): Response
    {
        return $this->twig->render($response, 'app/read.twig', $args);
    }
}
