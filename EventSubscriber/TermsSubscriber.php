<?php

/*
 * This file is part of itk-dev/terms-bundle.
 *
 * (c) 2018 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\TermsBundle\EventSubscriber;

use FOS\UserBundle\Model\User;
use ItkDev\TermsBundle\Helper\TermsHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class TermsSubscriber implements EventSubscriberInterface
{
    /** @var RequestStack */
    private $requestStack;

    /** @var TokenStorage */
    private $tokenStorage;

    /** @var \ItkDev\TermsBundle\Helper\TermsHelper */
    private $helper;

    public function __construct(
        RequestStack $requestStack,
        TokenStorage $tokenStorage,
        TermsHelper $helper
    ) {
        $this->requestStack = $requestStack;
        $this->tokenStorage = $tokenStorage;
        $this->helper = $helper;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'checkTermsrequest',
        ];
    }

    public function checkTermsrequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST !== $event->getRequestType()) {
            // don't do anything if it's not the master request
            return;
        }

        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        // Skip check on terms routes (user must be able to perform a request
        // that saves his accept of Terms).
        if (\in_array($request->get('_route'), ['itk_dev_terms_show', 'itk_dev_terms_accept'], true)) {
            return;
        }

        $user = $token->getUser();
        if ($user instanceof User && !$this->helper->isTermsAccepted($user)) {
            $redirectUrl = $this->helper->getRedirectUrl();

            $currentPath = $request->getPathInfo();
            $redirectInfo = parse_url($redirectUrl);

            // Only redirect if not already on redirect target path.
            $doRedirect = $redirectInfo['path'] !== $currentPath;

            if ($doRedirect) {
                // Add current url to redirect url.
                $referrer = $request->getPathInfo();
                if (null !== $request->getQueryString()) {
                    $referrer .= '?'.$request->getQueryString();
                }
                $redirectUrl .= (false === strpos($redirectUrl, '?') ? '?' : '&')
                  .'referrer='.urlencode($referrer);
                $event->setResponse(new RedirectResponse($redirectUrl));
            }
        }
    }
}
