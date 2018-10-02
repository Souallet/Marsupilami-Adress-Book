<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Annotation\Method;
use AppBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FriendController extends Controller
{

    public function __construct(){}

    /**
     * Create a new friend relation.
     *
     * @Route("/friends/add", name="friend_add", methods={"POST"})
     */
    public function addAction(Request $request)
    {
        $response = '';
        $currentUser = $this->getUser();
        $currentUserFriends = $currentUser->getMyFriends();

        $addForm = $this->createFormBuilder()
                        ->setAction($this->generateUrl('friend_add'))
                        ->setMethod('POST')
                        ->add('username', TextType::Class, array('attr' => array('placeholder' => 'Nom d\'utilisateur') ))
                        ->getForm();

        $addForm->handleRequest($request);

        if($addForm->isSubmitted() && $addForm->isValid()){
            $searchedUsername = $addForm['username']->getData();
            // Get the entity manager
            $entityManager = $this->getDoctrine()->getManager();
            // Get the user to delete by his id
            $userToAdd = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('username' => $searchedUsername));

            if (in_array($userToAdd, $currentUserFriends)) {
                $response = 'Vous êtes déjà ami.';
            } else {
                // Add the user to currentUser's FriendList
                $currentUser->addMyFriend($userToAdd);
                // Save Modification
                $entityManager->persist($currentUser);
                $entityManager->flush();
                // Reponse back
                $response = "L'utilisateur " . $userToAdd->getUsername() . " a bien été ajouté à votre liste d'ami.";
            }
        }

        // Retourne à la page 'profile'
        return $this->redirectToRoute('fos_user_profile_show', array('response' => $response));

    }

    /**
     * Delete a friend relation.
     *
     * @Route("/friends/del/{idToDel}", name="friend_delete")
     */
    public function delAction($idToDel)
    {
        $response = '';
        $currentUser = $this->getUser();
        $currentUserFriends = $currentUser->getMyFriends();

        // Get the entity manager
        $entityManager = $this->getDoctrine()->getManager();
        // Get the user to delete by his id
        $userToDel = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('id' => $idToDel));

        if (!in_array($userToDel, $currentUserFriends)) {
            $response = 'Vous n\’êtes pas ami, impossible de le supprimer';
        } else {
            // Del the user of currentUser's FriendList
            $currentUser->removeMyFriend($userToDel);
            // Save Modification
            $entityManager->persist($currentUser);
            $entityManager->flush();
            // Reponse back
            $response = "L'utilisateur " . $userToDel->getUsername() . " a bien été supprimé de votre liste d'ami.";
        }

        // Retourne à la page 'profile'
        return $this->redirectToRoute('fos_user_profile_show', array('response' => $response));
    }
}