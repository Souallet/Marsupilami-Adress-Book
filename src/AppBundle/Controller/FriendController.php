<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\User;

class FriendController extends Controller
{
    /**
     * Create a new friend relation.
     *
     * @Route("/friends/add", name="add_friend")
     */
    public function addAction(Request $request)
    {
        $response = '';
        $currentUser = $this->getUser();
        $currentUserFriends = $currentUser->getMyFriends();
        $idToAdd = 3;

        // Get the entity manager
        $entityManager = $this->getDoctrine()->getManager();
        // Get the user to delete by his id
        $userToAdd = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('id' => $idToAdd));

        if (in_array($userToAdd, $currentUserFriends)) {
            $response = 'Vous êtes déjà ami';
        } else {
            // Add the user to currentUser's FriendList
            $currentUser->addMyFriend($userToAdd);
            // Save Modification
            $entityManager->persist($currentUser);
            $entityManager->flush();
            // Reponse back
            $response = "L'utilisateur " . $userToAdd->getUsername() . " a bien été ajouté à votre liste d'ami.";
        }

        // Retourne à la page 'profile'
        return $this->redirectToRoute('fos_user_profile_show', array('response' => $response));
    }

    /**
     * Delete a friend relation.
     *
     * @Route("/friends/del/{idToDel}", name="del_friend")
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