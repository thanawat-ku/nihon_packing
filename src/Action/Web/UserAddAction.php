<?php

namespace App\Action\Web;

use App\Domain\User\Service\UserCreator;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class UserAddAction
{
    /**
     * @var Responder
     */
    private $responder;

    /**
     * @var UserUpdater
     */
    private $userCreator;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     * @param UserUpdater $userUpdater The service
     */
    public function __construct(Responder $responder, UserCreator $userCreator)
    {
        $this->responder = $responder;
        $this->userCreator = $userCreator;
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

        // Invoke the Domain with inputs and retain the result
        $this->userCreator->createUser( $data);

        // Build the HTTP response
        return $this->responder->withRedirect($response,"users");
    }
}
