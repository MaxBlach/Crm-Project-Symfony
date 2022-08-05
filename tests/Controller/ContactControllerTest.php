<?php

namespace App\Test\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ContactRepository $repository;
    private string $path = '/contact/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Contact::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Contact index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        //$this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'contact[firstname]' => 'Testing',
            'contact[lastname]' => 'Testing',
            'contact[email]' => 'Testing',
            'contact[phoneNumber]' => 'Testing',
        ]);

        self::assertResponseRedirects('/contact/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        //$this->markTestIncomplete();
        $fixture = new Contact();
        $fixture->setFirstname('My Title');
        $fixture->setLastname('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPhoneNumber('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Contact');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        //$this->markTestIncomplete();
        $fixture = new Contact();
        $fixture->setFirstname('My Title');
        $fixture->setLastname('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPhoneNumber('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'contact[firstname]' => 'Something New',
            'contact[lastname]' => 'Something New',
            'contact[email]' => 'Something New',
            'contact[phoneNumber]' => 'Something New',
        ]);

        self::assertResponseRedirects('/contact/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getFirstname());
        self::assertSame('Something New', $fixture[0]->getLastname());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getPhoneNumber());
    }

    public function testRemove(): void
    {
        //$this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Contact();
        $fixture->setFirstname('My Title');
        $fixture->setLastname('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPhoneNumber('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/contact/');
    }
}
