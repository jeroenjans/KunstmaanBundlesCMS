<?php

namespace Kunstmaan\AdminListBundle\Tests\DependencyInjection;

use Kunstmaan\AdminListBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigurationTest
 */
class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testProcessedValueContainsRequiredValue()
    {
        $array = [
            'lock' => [
                'enabled' => true,
                'check_interval' => 15,
                'threshold' => 35,
            ],
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}
