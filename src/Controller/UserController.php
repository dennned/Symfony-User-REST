<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserController constructor.
     * @param UserRepository $userRepository
     * @param LoggerInterface $logger
     */
    public function __construct(UserRepository $userRepository, LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getUsersAction(): JsonResponse
    {
        $users = $this->userRepository-> findAll();

        $formattedUsers = [];
        foreach ($users as $user) {
            $formattedUsers[] = [
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'creationdate' => $user->getCreationdate(),
                'updatedate' => $user->getUpdatedate()
            ];
        }

        $this->logger->info('CALL getUsersAction',[$formattedUsers]);

        return new JsonResponse($formattedUsers, Response::HTTP_OK);
    }

    /**
     * @Route("/new", name="user_new", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addUserAction(Request $request)
    {
        $user = new User();
        $post = $request->getContent();

        if (empty($post)) {
            return new JsonResponse(['message' => 'Not data user'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($post, true);

        if (!isset($data['firstname']) || !isset($data['lastname']) || !isset($data['creationdate'])) {
            return new JsonResponse(['message' => 'Fields firstname, lastname and creationdate are required'], Response::HTTP_NOT_FOUND);
        }

        list($firstname, $lastname, $dateCreation) = [$data['firstname'], $data['lastname'], $data['creationdate']];

        if (empty($firstname) || empty($lastname) || empty($dateCreation)) {
            return new JsonResponse(['message' => 'Values not be empty'], Response::HTTP_NOT_FOUND);
        }

        try {
            $user
                ->setFirstname($firstname)
                ->setLastname($lastname)
                ->setCreationdate(new \DateTime($dateCreation))
                ->setUpdatedate(new \DateTime('now'));
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage().' FORMAT = yyyy-mm-dd' ], Response::HTTP_NOT_FOUND);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $this->logger->info('CALL addUserAction USER '.$user->getId().' has added',[$data]);

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getUserAction(int $id): JsonResponse
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $user = $this->userRepository->find($id);

        if (null === $user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $jsonContent = $serializer->serialize($user, 'json');

        $this->logger->info('CALL getUserAction',[$jsonContent]);

        return new JsonResponse(['data' => $jsonContent], Response::HTTP_OK);

        // @TODO or without serializer
        /*$formattedUser[] = [
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'creationdate' => $user->getCreationdate(),
            'updatedate' => $user->getUpdatedate()
        ];

        return new JsonResponse($formattedUser, Response::HTTP_OK);*/
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"PUT"})
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editUserAction(Request $request, int $id)
    {
        $user = $this->userRepository->find($id);

        if (null === $user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $put = $request->getContent();

        if (empty($put)) {
            return new JsonResponse(['message' => 'Not data user'], Response::HTTP_NOT_FOUND);
        }

        // @TODO we can use Serializer
        $data = json_decode($put, true);

        if (!isset($data['firstname']) || !isset($data['lastname']) || !isset($data['creationdate'])) {
            return new JsonResponse(['message' => 'Fields firstname, lastname and creationdate are required'], Response::HTTP_NOT_FOUND);
        }

        list($firstname, $lastname, $dateCreation) = [$data['firstname'], $data['lastname'], $data['creationdate']];

        if (empty($firstname) || empty($lastname) || empty($dateCreation)) {
            return new JsonResponse(['message' => 'Values not be empty'], Response::HTTP_NOT_FOUND);
        }

        try {
            $user
                ->setFirstname($firstname)
                ->setLastname($lastname)
                ->setCreationdate(new \DateTime($dateCreation))
                ->setUpdatedate(new \DateTime('now'));
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage().' FORMAT = yyyy-mm-dd' ], Response::HTTP_NOT_FOUND);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $this->logger->info('CALL editUserAction USER '.$user->getId().' has modified ',[$data]);

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     *
     * @param int $id
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUserAction(int $id)
    {
        $user = $this->userRepository->find($id);

        if (null === $user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $this->logger->info('CALL deleteUserAction USER '.$user->getId().' has deleted ');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('user_index');
    }
}
