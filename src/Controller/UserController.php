<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
    {
        $this->userRepository = $userRepository;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/users", name="add_user", methods={"POST"})
     */
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $newUser = new User();
        $hashedPassword = $this->encoder->encodePassword($newUser, $data['password']);
        $password = $hashedPassword;

        $name = $data['name'];
        $username = $data['username'];
        $role = $data['role'];
        $rawPassword = $data['password'];

        $input = [
            'name' => $name,
            'username' => $username,
            'password' => $rawPassword,
            'role' => $role,
        ];

        $constraints = new Assert\Collection([
            'name' => [new Assert\Length(['min' => 2]), new Assert\NotBlank],
            'username' => [new Assert\Length(['min' => 3]), new Assert\NotBlank],
            'password' => [new Assert\Length(['min' => 4]), new Assert\NotBlank],
            'role' => [new Assert\NotBlank]
        ]);

        $violations = $validator->validate($input, $constraints);
        $errorMessages = [];
        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();

            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages, $violation->getPropertyPath(), $violation->getMessage());
            }

            return new JsonResponse($errorMessages, Response::HTTP_BAD_REQUEST);
        }

        // Check for existing username
        $user = $this->userRepository->findOneBy(['username' => $username]);
        if($user){
            $errorMessages = [
                'username already exist'
            ];
            return new JsonResponse($errorMessages, Response::HTTP_OK);
        }

        // Save the user
        $this->userRepository->saveUser($name, $username, $role, $password);

        // Return success response after creating user
        return new JsonResponse(['status' => 'User created!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/users", name="get_all_users", methods={"GET"})
     */
    public function readAll(): JsonResponse
    {
        $users = $this->userRepository->findAll();
        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'username' => $user->getUsername(),
                'role' => $user->getRoles(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/users/{id}", name="get_one_user", methods={"GET"})
     */
    public function readOne($id): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        $data = [];

        if($user){
            $data = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'username' => $user->getUsername(),
                'role' => $user->getRoles(),
            ];

            return new JsonResponse($data, Response::HTTP_OK);
        }

        $data = [
            'error' => [
                'message' => 'User not found'
            ]
        ];

        return new JsonResponse($data, Response::HTTP_NOT_FOUND);
    }

    /**
     * @Route("/users/{id}", name="update_user", methods={"PUT"})
     */
    public function update(Request $request, $id, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            $message = ['No user with id '.$id];
            return new JsonResponse($message, Response::HTTP_NOT_FOUND);
        }

        if (!empty($data['name'])) {
            $user->setName($data['name']);
        }

        if (!empty($data['username'])) {
            // Check for existing username
            $user = $this->userRepository->findOneBy(['username' => $data['username']]);
            $userData = [];

            $userData = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'username' => $user->getUsername(),
                'role' => $user->getRoles(),
            ];

            if($userData['id'] != $id){
                $errorMessages = [
                    'username already exist'
                ];

                return new JsonResponse($errorMessages, Response::HTTP_OK);
            }

            $user->setUsername($data['username']);
        }

        if (!empty($data['role'])) {
            $user->setRoles($data['role']);
        }

        if (!empty($data['password'])) {
            $newUser = new User();
            $hashedPassword = $this->encoder->encodePassword($newUser, $data['password']);
            $user->setPassword($hashedPassword);
        }

        $entityManager->flush();

        return new JsonResponse($user->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/users/{id}", name="delete_user", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);

        if (!$user) {
            $message = ['No user with id '.$id];
            return new JsonResponse($message, Response::HTTP_NOT_FOUND);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(['status' => 'User deleted'], Response::HTTP_OK);
    }
}
