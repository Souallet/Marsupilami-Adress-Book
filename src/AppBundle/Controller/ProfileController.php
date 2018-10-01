<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\ProfileController as BaseController;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

use FOS\UserBundle\Model\UserInterface;


class ProfileController extends BaseController
{

    public function __construct()
    {
    }

    public function showAction()
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $friends = $user->getMyFriends();

        // var_dump(is_object($user));
        // var_dump($user instanceof User);

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('@FOSUser/Profile/show.html.twig', array(
            'user' => $user,
            'friends' => $friends
        ));
    }

    
}