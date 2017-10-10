<?php

namespace Lofite\LofiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Security;

class SecurityController extends Controller
{
    public function loginAction()
    {
        if ($this->get('request')->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $this->get('request')->attributes->get(Security::AUTHENTICATION_ERROR);
        } else {
            $error = $this->get('request')->getSession()->get(Security::AUTHENTICATION_ERROR);
        }

        return $this->render('LofiteBaseBundle:Security:login.html.twig', array(
            'last_username' => $this->get('request')->getSession()->get(Security::LAST_USERNAME),
            'error' => $error
        ));
    }
}

?>