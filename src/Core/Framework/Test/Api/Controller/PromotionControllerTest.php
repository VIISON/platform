<?php
declare(strict_types=1);

namespace Shopware\Core\Framework\Test\Api\Controller;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Promotion\PromotionEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Test\TestCaseBase\AdminFunctionalTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\PlatformRequest;

class PromotionControllerTest extends TestCase
{
    use AdminFunctionalTestBehaviour;

    /**
     * @var EntityRepositoryInterface
     */
    private $promotionRepository;

    protected function setUp(): void
    {
        $this->promotionRepository = $this->getContainer()->get('promotion.repository');
    }

    /**
     * @group promotions
     *
     * @throws \Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException
     */
    public function testCreatePromotion(): void
    {
        $context = Context::createDefaultContext();
        $id = Uuid::randomHex();
        $absoluteDiscountId = Uuid::randomHex();
        $otherId = Uuid::randomHex();

        $data = [
            'id' => $id,
            'name' => 'My promotion',
            'active' => true,
            'validFrom' => '2019-01-01 00:00:00',
            'validUntil' => '2030-01-01 00:00:00',
            'redeemable' => 1000,
            'exclusive' => false,
            'priority' => 100,
            'excludeLowerPriority' => false,
            'codeType' => 'standard',
            'code' => 'PROMOTIONCODE',
            'discounts' => [
                [
                    'id' => $absoluteDiscountId,
                    'type' => 'absolute',
                    'value' => 100,
                    'graduated' => false,
                    'scope' => 'cart',
                ],
            ],
        ];

        $this->getClient()->request('POST', '/api/v' . PlatformRequest::API_VERSION . '/promotion', $data);

        static::assertSame(
            204,
            $this->getClient()->getResponse()->getStatusCode(),
            $this->getClient()->getResponse()->getContent()
        );

        $criteria = new Criteria([$id]);
        $criteria->addAssociation('discounts');

        /** @var PromotionEntity $promotion */
        $promotion = $this->promotionRepository->search($criteria, $context)->get($id);

        $discounts = $promotion->getDiscounts();
        static::assertCount(1, $discounts);
        static::assertTrue($discounts->has($absoluteDiscountId));
        static::assertFalse($discounts->has($otherId));

        $absoluteDiscount = $discounts->get($absoluteDiscountId);

        static::assertEquals(100, $absoluteDiscount->getValue());
        static::assertEquals('absolute', $absoluteDiscount->getType());
        static::assertEquals('cart', $absoluteDiscount->getScope());
    }

    /**
     * @group promotions
     */
    public function testReadPromotionList(): void
    {
        $client = $this->getClient();
        $context = Context::createDefaultContext();
        $id = Uuid::randomHex();
        $absoluteDiscountId = Uuid::randomHex();

        $this->promotionRepository->create([
            [
                'id' => $id,
                'name' => 'My promotion',
                'active' => true,
                'validFrom' => '2019-01-01 00:00:00',
                'validUntil' => '2030-01-01 00:00:00',
                'redeemable' => 1000,
                'exclusive' => false,
                'priority' => 100,
                'excludeLowerPriority' => false,
                'codeType' => 'standard',
                'code' => 'PROMOTIONCODE',
                'discounts' => [
                    [
                        'id' => $absoluteDiscountId,
                        'type' => 'absolute',
                        'value' => 100,
                        'graduated' => false,
                        'scope' => 'cart',
                    ],
                ],
            ],
        ],
            $context);

        $client->request('GET', '/api/v' . PlatformRequest::API_VERSION . '/promotion');

        static::assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());

        $content = json_decode($client->getResponse()->getContent(), true);

        static::assertNotEmpty($content);
        static::assertArrayHasKey('meta', $content);
        static::assertArrayHasKey('data', $content);
        static::assertGreaterThan(0, $content['meta']['total']);
        static::assertNotEmpty($content['data']);

        foreach ($content['data'] as $promotion) {
            static::assertArrayHasKey('id', $promotion);
            static::assertEquals('promotion', $promotion['type']);
            static::assertArrayHasKey('attributes', $promotion);

            static::assertArrayHasKey('name', $promotion['attributes']);
            static::assertArrayHasKey('active', $promotion['attributes']);
            static::assertArrayHasKey('redeemable', $promotion['attributes']);
            static::assertArrayHasKey('priority', $promotion['attributes']);

            static::assertArrayHasKey('relationships', $promotion);
        }
    }

    /**
     * @group promotions
     */
    public function testReadPromotionDetail(): void
    {
        $client = $this->getClient();
        $context = Context::createDefaultContext();
        $id = Uuid::randomHex();
        $absoluteDiscountId = Uuid::randomHex();

        $this->promotionRepository->create(
            [
                [
                    'id' => $id,
                    'name' => 'My promotion',
                    'active' => true,
                    'validFrom' => '2019-01-01 00:00:00',
                    'validUntil' => '2030-01-01 00:00:00',
                    'redeemable' => 1000,
                    'exclusive' => false,
                    'priority' => 100,
                    'excludeLowerPriority' => false,
                    'codeType' => 'standard',
                    'code' => 'PROMOTIONCODE',
                    'discounts' => [
                        [
                            'id' => $absoluteDiscountId,
                            'type' => 'absolute',
                            'value' => 100,
                            'graduated' => false,
                            'scope' => 'cart',
                        ],
                    ],
                ],
            ],
            $context
        );

        $client->request('GET', '/api/v' . PlatformRequest::API_VERSION . '/promotion/' . $id);

        static::assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());

        $content = json_decode($client->getResponse()->getContent(), true);

        static::assertNotEmpty($content);
        static::assertArrayHasKey('data', $content);
        static::assertNotEmpty($content['data']);

        $promotion = $content['data'];

        static::assertArrayHasKey('id', $promotion);
        static::assertEquals('promotion', $promotion['type']);
        static::assertArrayHasKey('attributes', $promotion);

        static::assertArrayHasKey('name', $promotion['attributes']);
        static::assertArrayHasKey('active', $promotion['attributes']);
        static::assertArrayHasKey('redeemable', $promotion['attributes']);
        static::assertArrayHasKey('priority', $promotion['attributes']);

        static::assertArrayHasKey('relationships', $promotion);
    }

    /**
     * @group promotions
     */
    public function testPatchPromotion(): void
    {
        $client = $this->getClient();
        $context = Context::createDefaultContext();
        $id = Uuid::randomHex();
        $absoluteDiscountId = Uuid::randomHex();

        $this->promotionRepository->create(
            [
                [
                    'id' => $id,
                    'name' => 'My promotion',
                    'active' => true,
                    'validFrom' => '2019-01-01 00:00:00',
                    'validUntil' => '2030-01-01 00:00:00',
                    'redeemable' => 1000,
                    'exclusive' => false,
                    'priority' => 100,
                    'excludeLowerPriority' => false,
                    'codeType' => 'standard',
                    'code' => 'PROMOTIONCODE',
                    'discounts' => [
                        [
                            'id' => $absoluteDiscountId,
                            'type' => 'absolute',
                            'value' => 100,
                            'graduated' => false,
                            'scope' => 'cart',
                        ],
                    ],
                ],
            ],
            $context
        );

        $data = [
            'name' => 'Patched promotion name',
            'active' => false,
            'discounts' => [
                [
                    'id' => $absoluteDiscountId,
                    'value' => 200,
                ],
            ],
        ];

        $client->request('PATCH', '/api/v' . PlatformRequest::API_VERSION . '/promotion/' . $id, $data);
        static::assertEquals(204, $client->getResponse()->getStatusCode());

        $criteria = new Criteria([$id]);
        $criteria->addAssociation('discounts');

        /** @var PromotionEntity $promotion */
        $promotion = $this->promotionRepository->search($criteria, $context)->get($id);

        static::assertCount(1, $promotion->getDiscounts());
        static::assertEquals('Patched promotion name', $promotion->getName());

        $discount = $promotion->getDiscounts()->get($absoluteDiscountId);

        static::assertEquals(200, $discount->getValue());
    }

    /**
     * @group promotions
     */
    public function testDeleteDiscount(): void
    {
        $client = $this->getClient();
        $context = Context::createDefaultContext();
        $id = Uuid::randomHex();
        $absoluteDiscountId = Uuid::randomHex();

        $this->promotionRepository->create(
            [
                [
                    'id' => $id,
                    'name' => 'My promotion',
                    'active' => true,
                    'validFrom' => '2019-01-01 00:00:00',
                    'validUntil' => '2030-01-01 00:00:00',
                    'redeemable' => 1000,
                    'exclusive' => false,
                    'priority' => 100,
                    'excludeLowerPriority' => false,
                    'codeType' => 'standard',
                    'code' => 'PROMOTIONCODE',
                    'discounts' => [
                        [
                            'id' => $absoluteDiscountId,
                            'type' => 'absolute',
                            'value' => 100,
                            'graduated' => false,
                            'scope' => 'cart',
                        ],
                    ],
                ],
            ],
            $context
        );

        $client->request(
            'DELETE',
            '/api/v' . PlatformRequest::API_VERSION . '/promotion/' . $id . '/discounts/' . $absoluteDiscountId
        );
        static::assertEquals(204, $client->getResponse()->getStatusCode());

        $criteria = new Criteria([$id]);
        $criteria->addAssociation('discounts');

        /** @var PromotionEntity $promotion */
        $promotion = $this->promotionRepository->search($criteria, $context)->get($id);

        static::assertCount(0, $promotion->getDiscounts());
    }

    public function patchDiscount(): void
    {
        $client = $this->getClient();
        $context = Context::createDefaultContext();
        $id = Uuid::randomHex();
        $absoluteDiscountId = Uuid::randomHex();

        $this->promotionRepository->create(
            [
                [
                    'id' => $id,
                    'name' => 'My promotion',
                    'active' => true,
                    'validFrom' => '2019-01-01 00:00:00',
                    'validUntil' => '2030-01-01 00:00:00',
                    'redeemable' => 1000,
                    'exclusive' => false,
                    'priority' => 100,
                    'excludeLowerPriority' => false,
                    'codeType' => 'standard',
                    'code' => 'PROMOTIONCODE',
                    'discounts' => [
                        [
                            'id' => $absoluteDiscountId,
                            'type' => 'absolute',
                            'value' => 100,
                            'graduated' => false,
                            'scope' => 'cart',
                        ],
                    ],
                ],
            ],
            $context
        );

        $data = [
            'type' => 'percentage',
            'value' => 10,
        ];

        $client->request(
            'PATCH',
            '/api/v' . PlatformRequest::API_VERSION . '/promotion' . $id . '/discounts/' . $absoluteDiscountId,
            $data
        );

        $criteria = new Criteria([$id]);
        $criteria->addAssociation('discounts');

        /** @var PromotionEntity $promotion */
        $promotion = $this->promotionRepository->search($criteria, $context)->get($id);

        $discount = $promotion->getDiscounts()->get($absoluteDiscountId);

        static::assertEquals('percentage', $discount->getType());
        static::assertSame(10, $discount->getValue());
    }

    /**
     * @group promotions
     */
    public function testDeletePromotion(): void
    {
        $client = $this->getClient();
        $context = Context::createDefaultContext();
        $id = Uuid::randomHex();
        $absoluteDiscountId = Uuid::randomHex();

        $this->promotionRepository->create(
            [
                [
                    'id' => $id,
                    'name' => 'My promotion',
                    'active' => true,
                    'validFrom' => '2019-01-01 00:00:00',
                    'validUntil' => '2030-01-01 00:00:00',
                    'redeemable' => 1000,
                    'exclusive' => false,
                    'priority' => 100,
                    'excludeLowerPriority' => false,
                    'codeType' => 'standard',
                    'code' => 'PROMOTIONCODE',
                    'discounts' => [
                        [
                            'id' => $absoluteDiscountId,
                            'type' => 'absolute',
                            'value' => 100,
                            'graduated' => false,
                            'scope' => 'cart',
                        ],
                    ],
                ],
            ],
            $context
        );

        $client->request('DELETE', '/api/v' . PlatformRequest::API_VERSION . '/promotion/' . $id);
        static::assertEquals(204, $client->getResponse()->getStatusCode());

        $criteria = new Criteria([$id]);
        $criteria->addAssociation('discounts');

        $promotions = $this->promotionRepository->search($criteria, $context);
        static::assertFalse($promotions->has($id));
    }
}
