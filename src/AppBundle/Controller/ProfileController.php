<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\ProfileController as BaseController;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use AppBundle\Entity\User;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Controller\FriendController;


class ProfileController extends BaseController
{

    public function __construct()
    {
    }

    public function showAction()
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $friends = $user->getMyFriends();
        $deleteForms = array();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $addForm = $this->createFormBuilder()
                        ->setAction($this->generateUrl('friend_add'))
                        ->setMethod('POST')
                        ->add('username', TextType::Class, array('attr' => array('placeholder' => 'Nom d\'utilisateur') ))
                        ->getForm();

        foreach ($friends as $entity) {
            $deleteForms[$entity->getId()] = $this->createDeleteForm($entity)->createView();
        }
   
        return $this->render('@FOSUser/Profile/show.html.twig', array(
            'user' => $user,
            'friends' => $friends,
            'deleteForms' => $deleteForms,
            'addForm' => $addForm->createView(),
        ));
    }

    private function createDeleteForm(User $friend)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('friend_delete', array('idToDel' => $friend->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
}