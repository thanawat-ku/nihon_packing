<?php

namespace App\Action\Web;

use App\Domain\User\Service\UserUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class UserEditAction
{
    /**
     * @var Responder
     */
    private $responder;

    /**
     * @var UserUpdater
     */
    private $userUpdater;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     * @param UserUpdater $userUpdater The service
     */
    public function __construct(Responder $responder, UserUpdater $userUpdater)
    {
        $this->responder = $responder;
        $this->userUpdater = $userUpdater;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     * @param array $args The route arguments
     *
     * @return ResponseInterface The response
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        // Extract the form data from the request body
        $data = (array)$request->getParsedBody();
        $userId = $data["id"];

        // Invoke the Domain with inputs and retain the result
        $this->userUpdater->updateUser($userId, $data);

        // Build the HTTP response
        return $this->responder->withRedirect($response,"users");
    }
}
