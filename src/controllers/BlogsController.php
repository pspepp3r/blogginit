<?php

declare(strict_types=1);

namespace Src\Controllers;

use Slim\Views\Twig;
use Src\Services\BlogService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class BlogsController
{
    public function __construct(
        private readonly BlogService $blogService,
        private readonly Twig $twig
    ) {}

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

    public function renderBlog(Request $request, Response $response, array $args): Response
    {
        if (!$blog = $this->blogService->addView($args['uuid'])) {
            return $response->withHeader('Location', '/error?code=404&message=Blog not found')->withStatus(302);
        }

        $args = [
            'blog' => $blog
        ];
        return $this->twig->render($response, 'app/read.twig', $args);
    }

    public function deleteBlog(Request $request, Response $response, array $args): Response
    {
        // $blog = $this->blogService->getRequestUUId($request);

        $this->blogService->delete($args['uuid']);

        return $response->withHeader('Location', '/blogs')->withStatus(302);
    }
}
