<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class PostController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer    = $serializer;
    }

    /**
     * @Route("/post", name="get_all_post", methods={"GET"})
     */
    public function getAllPostAction(): Response
    {
        $posts = $this->entityManager->getRepository(Post::class)->findAll();

        return new Response($this->serializer->serialize($posts, 'json'), Response::HTTP_OK);
    }

    /**
     * @Route("/post", name="create_post", methods={"POST"})
     */
    public function createPostAction(Request $request): Response
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $post = new Post($data['title'], $data['content']);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return new JsonResponse(['id' => $post->getId()], Response::HTTP_OK);
    }

    /**
     * @Route("/post/{id}", name="edit_post", methods={"PATCH"})
     * @ParamConverter("post", class="App\Entity\Post")
     */
    public function editPostAction(Request $request, Post $post): Response
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $post->setTitle($data['title']);
        $post->setContent($data['content']);

        $this->entityManager->flush();

        return new JsonResponse(['id' => $post->getId()], Response::HTTP_OK);
    }

    /**
     * @Route("/post/{id}", name="delete_post", methods={"DELETE"})
     * @ParamConverter("post", class="App\Entity\Post")
     */
    public function deletePostAction(Post $post): Response
    {
        $this->entityManager->remove($post);
        $this->entityManager->flush();

        return new Response(null, Response::HTTP_OK);
    }
}
