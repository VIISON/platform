<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Test\Cart\Promotion;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Promotion\PromotionEntity;
use Shopware\Core\Content\Rule\RuleCollection;
use Shopware\Core\Content\Rule\RuleEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class PromotionEntityTest extends TestCase
{
    /**
     * @var PromotionEntity
     */
    private $promotion = null;

    /**
     * @var MockObject
     */
    private $checkoutContext = null;

    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        $rulePersona = new RuleEntity();
        $rulePersona->setId('PERSONA-1');

        $ruleScope = new RuleEntity();
        $ruleScope->setId('CART-1');

        $ruleOrder = new RuleEntity();
        $ruleOrder->setId('ORDER-1');

        $this->promotion = new PromotionEntity();
        $this->promotion->setPersonaRules(new RuleCollection([$rulePersona]));
        $this->promotion->setCartRules(new RuleCollection([$ruleScope]));
        $this->promotion->setOrderRules(new RuleCollection([$ruleOrder]));

        $this->checkoutContext = $this->getMockBuilder(SalesChannelContext::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * This test verifies that our validation allows the
     * promotion based on the persona rule. For this, the ruleID
     * has to occur in the current checkout context.
     *
     * @test
     * @group promotions
     */
    public function testPersonaRuleIsRecognizedInContext()
    {
        $checkoutRuleIds = [
            'OTHER-RULE',
            'PERSONA-1',
        ];

        $this->checkoutContext->expects(static::any())->method('getRuleIds')->willReturn($checkoutRuleIds);

        $isValid = $this->promotion->isPersonaConditionValid($this->checkoutContext);

        static::assertTrue($isValid);
    }

    /**
     * This test verifies that our validation prohibits the
     * promotion based on the persona rule.
     * In this case, our rule does not occur in the checkout context
     *
     * @test
     * @group promotions
     */
    public function testPersonaRuleIsNotInContext()
    {
        $contextRuleIDs = [
            'OTHER-RULE1',
            'OTHER-RULE2',
        ];

        $this->checkoutContext->expects(static::any())->method('getRuleIds')->willReturn($contextRuleIDs);

        $isValid = $this->promotion->isPersonaConditionValid($this->checkoutContext);

        static::assertFalse($isValid);
    }

    /**
     * If no persona rule has been set, then the
     * promotion is always valid.
     * This does just mean we have no restriction.
     *
     * @test
     * @group promotions
     */
    public function testPersonaRuleValidIfEmpty()
    {
        $checkoutRuleIDs = [
            'OTHER-RULE',
        ];

        $promotionWithoutRule = new PromotionEntity();

        $this->checkoutContext->expects(static::any())->method('getRuleIds')->willReturn($checkoutRuleIDs);

        $isValid = $promotionWithoutRule->isPersonaConditionValid($this->checkoutContext);

        static::assertTrue($isValid);
    }

    /**
     * This test verifies that our validation allows the
     * promotion based on the cart rule. For this, the ruleID
     * has to occur in the current checkout context.
     *
     * @test
     * @group promotions
     */
    public function testCartRuleIsRecognizedInContext()
    {
        $checkoutRuleIds = [
            'OTHER-RULE',
            'CART-1',
        ];

        $this->checkoutContext->expects(static::any())->method('getRuleIds')->willReturn($checkoutRuleIds);

        $isValid = $this->promotion->isCartConditionValid($this->checkoutContext);

        static::assertTrue($isValid);
    }

    /**
     * This test verifies that our validation prohibits the
     * promotion based on the cart rule.
     * In this case, our rule does not occur in the checkout context.
     *
     * @test
     * @group promotions
     */
    public function testCartRuleIsNotInContext()
    {
        $checkoutRuleIDs = [
            'OTHER-RULE1',
            'OTHER-RULE2',
        ];

        $this->checkoutContext->expects(static::any())->method('getRuleIds')->willReturn($checkoutRuleIDs);

        $isValid = $this->promotion->isCartConditionValid($this->checkoutContext);

        static::assertFalse($isValid);
    }

    /**
     * If no scope rule has been set, then the promotion is always
     * valid within the cart check.
     * This does just mean we have no restriction.
     *
     * @test
     * @group promotions
     */
    public function testCartRuleValidIfEmpty()
    {
        $checkoutRuleIDs = [
            'OTHER-RULE',
        ];

        $promotionWithoutRule = new PromotionEntity();

        $this->checkoutContext->expects(static::any())->method('getRuleIds')->willReturn($checkoutRuleIDs);

        $isValid = $promotionWithoutRule->isCartConditionValid($this->checkoutContext);

        static::assertTrue($isValid);
    }

    /**
     * This test verifies that our validation allows the
     * promotion based on the order rule. For this, the ruleID
     * has to occur in the current checkout context.
     *
     * @test
     * @group promotions
     */
    public function testOrderRuleIsRecognizedInContext()
    {
        $checkoutRuleIds = [
            'OTHER-RULE',
            'ORDER-1',
        ];

        $this->checkoutContext->expects(static::any())->method('getRuleIds')->willReturn($checkoutRuleIds);

        $isValid = $this->promotion->isOrderConditionValid($this->checkoutContext);

        static::assertTrue($isValid);
    }

    /**
     * This test verifies that our validation prohibits the
     * promotion based on the order rule.
     * In this case, our rule does not occur in the checkout context
     *
     * @test
     * @group promotions
     */
    public function testOrderRuleIsNotInContext()
    {
        $contextRuleIDs = [
            'OTHER-RULE1',
            'OTHER-RULE2',
        ];

        $this->checkoutContext->expects(static::any())->method('getRuleIds')->willReturn($contextRuleIDs);

        $isValid = $this->promotion->isOrderConditionValid($this->checkoutContext);

        static::assertFalse($isValid);
    }

    /**
     * If no order rule has been set, then the
     * promotion is always valid.
     * This does just mean we have no restriction.
     *
     * @test
     * @group promotions
     */
    public function testOrderRulesValidIfEmpty()
    {
        $checkoutRuleIDs = [
            'OTHER-RULE',
        ];

        $promotionWithoutRule = new PromotionEntity();

        $this->checkoutContext->expects(static::any())->method('getRuleIds')->willReturn($checkoutRuleIDs);

        $isValid = $promotionWithoutRule->isOrderConditionValid($this->checkoutContext);

        static::assertTrue($isValid);
    }
}
