<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Component\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeDetails;
use Symfony\Component\Security\Http\RememberMe\RememberMeHandlerInterface;

/**
 * Used as a "workaround" for tagging aliases in the RememberMeFactory.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 *
 * @internal
 */
final class DecoratedRememberMeHandler implements RememberMeHandlerInterface
{
    private $handler;

    public function __construct(RememberMeHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * {@inheritDoc}
     */
    public function createRememberMeCookie(UserInterface $user): void
    {
        $this->handler->createRememberMeCookie($user);
    }

    /**
     * {@inheritDoc}
     */
    public function consumeRememberMeCookie(RememberMeDetails $rememberMeDetails): UserInterface
    {
        return $this->handler->consumeRememberMeCookie($rememberMeDetails);
    }

    /**
     * {@inheritDoc}
     */
    public function clearRememberMeCookie(): void
    {
        $this->handler->clearRememberMeCookie();
    }
}
