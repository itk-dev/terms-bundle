<?php

/*
 * This file is part of itk-dev/terms-bundle.
 *
 * (c) 2018 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\TermsBundle\Controller;

use ItkDev\TermsBundle\Helper\TermsHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TermsController extends Controller
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var \ItkDev\TermsBundle\Helper\TermsHelper */
    private $helper;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        TermsHelper $helper
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->helper = $helper;
    }

    public function showAction(Request $request)
    {
        $form = $this->createTermsForm($request->get('referrer'));

        return $this->render('ItkDevTermsBundle:Default:index.html.twig', ['form' => $form->createView()]);
    }

    public function acceptAction(Request $request)
    {
        $form = $this->createTermsForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && true === $form->get('accept')->getData()) {
            $token = $this->tokenStorage->getToken();
            if (null !== $token) {
                $user = $token->getUser();
                $this->helper->setTermsAccepted($user);

                $referrer = $form->get('referrer')->getData();

                return $this->redirect($referrer ?: '/');
            }
        }

        return $this->showAction();
    }

    private function createTermsForm($referrer = null)
    {
        return $this->createFormBuilder(['referrer' => $referrer])
            ->setAction($this->generateUrl('itk_dev_terms_accept'))
            ->setMethod('POST')
            ->add('accept', CheckboxType::class, [
                'required' => true,
                'label' => 'Accept Terms',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Accept',
            ])
          ->add('referrer', HiddenType::class)
            ->getForm();
    }
}
