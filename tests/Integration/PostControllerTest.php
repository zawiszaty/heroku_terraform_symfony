<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Entity\Post;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class PostControllerTest extends WebTestCase
{
    private EntityManager $em;

    private Router $router;

    private KernelBrowser $client;

    protected function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->client->disableReboot();

        $this->em = self::$container->get('doctrine.orm.entity_manager');

        $this->router = self::$container->get('router');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testItCreatePost(): void
    {
        $this->client->request(Request::METHOD_POST, $this->router->generate('create_post'), [], [], ['CONTENT_TYPE' => 'application/json'],
            '{"title":"test", "content": "test"}');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $post = $this->em->getRepository(Post::class)->findOneBy(['title' => 'test', 'content' => 'test']);
        $this->assertNotNull($post);
    }

    public function testItEditPost(): void
    {
        $post = $this->createPost();

        $this->client->request(Request::METHOD_PATCH, $this->router->generate('edit_post', ['id' => $post->getId()]), [], [], ['CONTENT_TYPE' => 'application/json'],
            '{"title":"new_title", "content": "new_content"}');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $editedPost = $this->em->getRepository(Post::class)->findOneBy([
            'title'   => 'new_title',
            'content' => 'new_content',
        ]);
        $this->assertNotNull($editedPost);
    }

    public function testItDeletePost(): void
    {
        $post = $this->createPost();

        $this->client->request(Request::METHOD_DELETE, $this->router->generate('delete_post', ['id' => $post->getId()]), [], [], ['CONTENT_TYPE' => 'application/json']);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertNull($post->getId());
    }

    public function testItReturnAllPost(): void
    {
        foreach (range(1, 10) as $item)
        {
            $this->createPost();
        }

        $this->client->request(Request::METHOD_GET, $this->router->generate('get_all_post'), [], [], ['CONTENT_TYPE' => 'application/json']);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $body = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(10, $body);
    }

    protected function createPost(): Post
    {
        $post = new Post('test', 'test');
        $this->em->persist($post);
        $this->em->flush();

        $post = $this->em->getRepository(Post::class)->findOneBy([
            'title'   => 'test',
            'content' => 'test',
        ]);

        return $post;
    }
}
