<?php declare(strict_types=1);

namespace Shopware\Tests\Integration\Core\Framework\App\Flow\FlowAction\Xml;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\App\Flow\Action\Action;
use Shopware\Core\Framework\Feature;

/**
 * @internal
 */
class ParameterTest extends TestCase
{
    public function testFromXml(): void
    {
        $flowActionsFile = Feature::isActive('v6.6.0.0') ? '/../_fixtures/valid/major/flow.xml' : '/../_fixtures/valid/minor/flow-action.xml';
        $flowActions = Action::createFromXmlFile(__DIR__ . $flowActionsFile);

        static::assertNotNull($flowActions->getActions());
        static::assertCount(1, $flowActions->getActions()->getActions());

        $firstAction = $flowActions->getActions()->getActions()[0];
        $firstHeaderParameter = $firstAction->getHeaders()->getParameters()[0];
        $firstParameter = $firstAction->getParameters()->getParameters()[0];

        static::assertEquals('string', $firstHeaderParameter->getType());
        static::assertEquals('content-type', $firstHeaderParameter->getName());
        static::assertEquals('application/json', $firstHeaderParameter->getValue());

        static::assertEquals('string', $firstParameter->getType());
        static::assertEquals('to', $firstParameter->getName());
        static::assertEquals('{{ customer.name }}', $firstParameter->getValue());
    }
}