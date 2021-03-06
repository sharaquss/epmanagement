<?php

namespace UserBundle\Controller;

use AestheticBundle\Containers\BootstrapNavbar;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\Session;
use AestheticBundle\Containers\NavbarHelperElements;

class SecurityController extends Controller
{
    /**
     * @Template
     */

  //  private $reg =

    public function loginAction() {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return array(
                'helper' => (new NavbarHelperElements())->createHelperLogin(),
                'navbarLeft' => (new BootstrapNavbar())->createNavbarLoginLeft(),
                'navbarRight' => (new BootstrapNavbar())->createNavbarLoginRight(),
                'last_username' => $lastUsername,
                'error'         => $error,
        );
    }

    public function loginFailureAction() {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $session = new Session();
        $session->getFlashBag()
            ->add('flash_error', 'Oops! Your username or password does not match or exist. Try again or register, please.');

        return array(
            'helper' => (new NavbarHelperElements())->createHelperLogin(),
            'navbarLeft' => (new BootstrapNavbar())->createNavbarLoginLeft(),
            'navbarRight' => (new BootstrapNavbar())->createNavbarLoginRight(),
            'last_username' => $lastUsername,
            'error'         => $error,
        );
    }

    public function loginCheckAction() {


    }


    public function logoutAction() {
        $session = new Session();
        $this->$session->getFlashbag()
            ->add('flash_success', 'COME BACK');
    }



}