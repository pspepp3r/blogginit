<?php

declare(strict_types=1);

namespace Src\Controllers;

use Slim\Views\Twig;
use Src\Services\BlogService;
use Src\Data_objects\CreateBlogData;
use Src\Services\ResponseFormatterService;
use Src\Validators\CreateBlogRequestValidator;
use Psr\Http\Message\ResponseInterface as Response;
use Src\Contracts\RequestValidatorFactoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Src\Entities\Blog;
use Src\Entities\Reply;
use Src\Entities\User;
use Src\Providers\UserProvider;
use Src\Services\CommentService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BlogsController
{
    public function __construct(
        private readonly BlogService $blogService,
        private readonly CommentService $commentService,
        private readonly ResponseFormatterService $responseFormatter,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly Twig $twig,
        private readonly UserProvider $userProvider
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
        /**
         * @var User
         */
        $user = $this->twig->getEnvironment()->getGlobals()['user'];

        if (!$user)
            $user = $request->getServerParams()['REMOTE_ADDR'];

        if (!$blog = $this->blogService->addView($args['uuid'], $user)) {
            return $response
                ->withHeader('Location', '/error?code=404&message=Blog not found')
                ->withStatus(302);
        }

        $args = [
            'blog' => $blog,
            'ticked' => $this->userProvider->ticked($user, $blog)
        ];
        return $this->twig->render($response, 'app/read.twig', $args);
    }

    public function deleteBlog(Response $response, array $args): Response
    {
        $this->blogService->delete($args['uuid']);

        return $response->withHeader('Location', '/blogs')->withStatus(302);
    }

    public function handleCreateBlog(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(CreateBlogRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        if ($this->blogService->create(
            new CreateBlogData(
                $this->twig->getEnvironment()->getGlobals()['user'],
                trim($data['title']),
                $data['content'],
                $data['category']
            )
        )) {
            return $response->withHeader('Location', '/blogs')->withStatus(302);
        }

        return $response->withHeader('Location', 'error?code=&message=Something went wrong')->withStatus(302);
    }

    public function toggleTick(Response $response, array $args): Response
    {
        if (!$user = $this->twig->getEnvironment()->getGlobals()['user'])
            return $this->responseFormatter->asJson($response, ['interaction_error' => true]);

        $this->blogService->toggleTick($args['uuid'], $user);
        return $this->responseFormatter->asJson($response, ['ok' => true]);
    }

    public function addComment(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        $user = $this->twig->getEnvironment()->getGlobals()['user'];

        $this->commentService->addComment($user, $args['uuid'], $data['comment']);
        return $response->withHeader('Location', '/blog/' . $args['uuid'])->withStatus(302);
    }
}
