<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $roles = $token->getRoleNames();

        if (in_array('ROLE_ADMIN', $roles)) {
            return new RedirectResponse($this->router->generate('dashboardAdmin'));
        }
        if (in_array('ROLE_MANAGER', $roles)) {
            return new RedirectResponse($this->router->generate('DashboardManager'));
        }
        if (in_array('ROLE_CAMARERO', $roles)) {
            return new RedirectResponse($this->router->generate('panel_principal'));
        }
        return new RedirectResponse($this->router->generate('ver_carta'));
    }
}
