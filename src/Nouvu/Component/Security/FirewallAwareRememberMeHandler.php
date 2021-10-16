<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Component\Security;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallAwareTrait;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeDetails;
use Symfony\Component\Security\Http\RememberMe\RememberMeHandlerInterface;

/**
 * Decorates {@see RememberMeHandlerInterface} for the current firewall.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
final class FirewallAwareRememberMeHandler implements RememberMeHandlerInterface
{
    use FirewallAwareTrait;

    private const FIREWALL_OPTION = 'remember_me';

    public function __construct(FirewallMap $firewallMap, ContainerInterface $rememberMeHandlerLocator, RequestStack $requestStack)
    {
        $this->firewallMap = $firewallMap;
        $this->locator = $rememberMeHandlerLocator;
        $this->requestStack = $requestStack;
    }

    public function createRememberMeCookie(UserInterface $user): void
    {
        $this->getForFirewall()->createRememberMeCookie($user);
    }

    public function consumeRememberMeCookie(RememberMeDetails $rememberMeDetails): UserInterface
    {
        return $this->getForFirewall()->consumeRememberMeCookie($rememberMeDetails);
    }

    public function clearRememberMeCookie(): void
    {
        $this->getForFirewall()->clearRememberMeCookie();
    }
}
