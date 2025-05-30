<?php
// tests/Service/FirebaseServiceTest.php
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FirebaseServiceTest extends KernelTestCase
{
    private $firebaseService;

    protected function setUp(): void
    {
        self::bootKernel(['environment' => 'test']);
        $this->firebaseService = self::getContainer()->get(FirebaseService::class);
    }

    public function testCreateDocument(): void
    {
        $data = ['name' => 'Test', 'email' => 'test@example.com'];
        $result = $this->firebaseService->createDocument('users', $data);

        // Métodos correctos para PHPUnit moderno
        $this->assertTrue($result->id() !== null);
        $this->assertSame($data['name'], $result->data()['name']);
    }
}
