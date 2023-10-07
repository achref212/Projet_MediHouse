<?php

namespace App\Security;

use App\Entity\User;
use App\Controller\RegistrationController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

use App\Security\app;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    public const LOGIN_ROUTE1 = 'app_login_Admin';
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        $User = new User();
        $user = $token->getUser();

        if (in_array('ROLE_DOCTOR', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('user_profile'));
        }

        if (in_array('ROLE_PATIENT', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_patient_index'));
        }

        if (in_array('ROLE_PARA', $user->getRoles(), true)) {

            return new RedirectResponse($this->urlGenerator->generate('user_profile'));
        }
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_Admin_index'));
        }

        // For example:
        // return new RedirectResponse($this->urlGenerator->generate('some_route'));
        throw new \Exception('TODO: provide a valid redirect inside ' . __FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
    protected function getLoginUrl1(Request $request1): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE1);
    }
}
