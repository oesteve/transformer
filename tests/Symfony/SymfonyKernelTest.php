<?php


namespace Oesteve\Tests\Transformer\Symfony;

use Oesteve\Tests\Transformer\Dto\UserDto;
use Oesteve\Transformer\Transformer;
use PHPUnit\Framework\TestCase;

class SymfonyKernelTest extends TestCase
{
    public function testKernel(): void
    {
        $kernel = new TestKernel('test', true);
        $kernel->boot();
        $this->assertEquals('test', $kernel->getEnvironment());

        /** @var Transformer $transformer */
        $transformer = $kernel->getContainer()->get(Transformer::class);
        $dto = $transformer->transform(UserDto::class, 'Bob');

        $this->assertEquals(new UserDto('Bob'), $dto);
    }
}
