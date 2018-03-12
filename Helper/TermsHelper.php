<?php

/*
 * This file is part of itk-dev/terms-bundle.
 *
 * (c) 2018 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\TermsBundle\Helper;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\RouterInterface;

class TermsHelper
{
    /** @var \FOS\UserBundle\Model\UserManagerInterface */
    private $userManager;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var \Symfony\Component\PropertyAccess\PropertyAccessor */
    private $accessor;

    /** @var array */
    private $configuration;

    public function __construct(
      UserManagerInterface $userManager,
      RouterInterface $router,
      PropertyAccessor $accessor,
      array $configuration
    ) {
        $this->userManager = $userManager;
        $this->router = $router;
        $this->accessor = $accessor;
        $this->configuration = $configuration;
    }

    public function isTermsAccepted(UserInterface $user)
    {
        $property = $this->getTermsAcceptedProperty();

        return $this->accessor->getValue($user, $property);
    }

    public function setTermsAccepted(UserInterface $user)
    {
        $property = $this->getTermsAcceptedProperty();
        $value = $this->getTermsAcceptedAcceptedValue();
        $this->accessor->setValue($user, $property, $value);
        $this->userManager->updateUser($user);
    }

    public function getRedirectUrl()
    {
        $redirectUrl = null;

        if (isset($this->configuration['accept_route'])) {
            $routeName = $this->configuration['accept_route'];
            $routeParameters = isset($this->configuration['accept_route_parameters'])
              ? $this->configuration['accept_route_parameters'] : [];
            $redirectUrl = $this->router->generate(
                $routeName,
              $routeParameters
            );
        } elseif (isset($this->configuration['accept_url'])) {
            $redirectUrl = $this->configuration['accept_url'];
        }

        if (null === $redirectUrl) {
            throw new \RuntimeException('Terms not configured correctly.');
        }

        return $redirectUrl;
    }

    private function getTermsAcceptedProperty()
    {
        return $this->configuration['user_terms_property'];
    }

    private function getTermsAcceptedAcceptedValue()
    {
        return new \DateTime();
    }
}
