<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\ErrorConstants;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Structure\NewRegistrationTransformed;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController {

    /**
     * Registration of new user
     * @Route("/rest/register", name="register", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function registration(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository, LoggerInterface $logger) {
        /** @var NewRegistrationTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new NewRegistrationStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        } else if ($userRepository->findOneBy(['nick' => $trans->getName()])) {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_NAME_IS_TAKEN));
        }
        $user = new User();
        $user->setNick($trans->getName());
        $user->setRoles(["ROLE_USER"]);
        if ($trans->getMail() !== null) {
            $user->setMail($trans->getMail());
        }
        try {
            $user->setPassword($passwordEncoder->encodePassword($user, $trans->getPassword()));
            $trans->setPassword('');
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (Exception $exception) {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_SOMETHING_GO_WRONG, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
        return ResponseHelper::jsonResponse(Message::createCreated());
    }

}
