<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Promotion;

use Shopware\Core\Checkout\Customer\CustomerCollection;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Promotion\Aggregate\PromotionDiscount\PromotionDiscountCollection;
use Shopware\Core\Checkout\Promotion\Aggregate\PromotionSalesChannel\PromotionSalesChannelCollection;
use Shopware\Core\Content\Rule\RuleCollection;
use Shopware\Core\Content\Rule\RuleEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class PromotionEntity extends Entity
{
    use EntityIdTrait;

    public const CODE_TYPE_NO_CODE = 'no_code';

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var \DateTimeInterface|null
     */
    protected $validFrom;

    /**
     * @var \DateTimeInterface|null
     */
    protected $validUntil;

    /**
     * @var int
     */
    protected $redeemable;

    /**
     * @var bool
     */
    protected $exclusive;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var bool
     */
    protected $useCodes;

    /**
     * @var bool
     */
    protected $excludeLowerPriority;

    /**
     * @var string|null
     */
    protected $scopeRuleId;

    /**
     * @var string|null
     */
    protected $discountRuleId;

    /**
     * @var RuleEntity|null
     */
    protected $discountRule;

    /**
     * @var PromotionSalesChannelCollection|null
     */
    protected $salesChannels;

    /**
     * @var string|null
     */
    protected $code;

    /**
     * @var PromotionDiscountCollection|null
     */
    protected $discounts;

    /**
     * @var RuleCollection|null
     */
    protected $orderRules;

    /**
     * @var RuleCollection|null
     */
    protected $personaRules;

    /**
     * @var CustomerCollection|null
     */
    protected $personaCustomers;

    /**
     * @var RuleCollection|null
     */
    protected $cartRules;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getValidFrom(): ?\DateTimeInterface
    {
        return $this->validFrom;
    }

    public function setValidFrom(\DateTimeInterface $validFrom): void
    {
        $this->validFrom = $validFrom;
    }

    public function getValidUntil(): ?\DateTimeInterface
    {
        return $this->validUntil;
    }

    public function setValidUntil(\DateTimeInterface $validUntil): void
    {
        $this->validUntil = $validUntil;
    }

    public function getRedeemable(): int
    {
        return $this->redeemable;
    }

    public function setRedeemable(int $redeemable): void
    {
        $this->redeemable = $redeemable;
    }

    public function isExclusive(): bool
    {
        return $this->exclusive;
    }

    public function setExclusive(bool $exclusive): void
    {
        $this->exclusive = $exclusive;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * Gets if the promotion requires codes
     * in order to be used
     */
    public function isUseCodes(): bool
    {
        return $this->useCodes;
    }

    /**
     * Sets if the promotion requires a code
     * to be used.
     */
    public function setUseCodes(bool $useCodes): void
    {
        $this->useCodes = $useCodes;
    }

    public function isExcludeLowerPriority(): bool
    {
        return $this->excludeLowerPriority;
    }

    public function setExcludeLowerPriority(bool $excludeLowerPriority): void
    {
        $this->excludeLowerPriority = $excludeLowerPriority;
    }

    public function getScopeRuleId(): ?string
    {
        return $this->scopeRuleId;
    }

    public function setScopeRuleId(string $scopeRuleId): void
    {
        $this->scopeRuleId = $scopeRuleId;
    }

    public function getDiscountRuleId(): ?string
    {
        return $this->discountRuleId;
    }

    public function setDiscountRuleId(string $discountRuleId): void
    {
        $this->discountRuleId = $discountRuleId;
    }

    public function getDiscountRule(): ?RuleEntity
    {
        return $this->discountRule;
    }

    public function setDiscountRule(RuleEntity $discountRule): void
    {
        $this->discountRule = $discountRule;
    }

    public function getDiscounts(): ?PromotionDiscountCollection
    {
        return $this->discounts;
    }

    public function setDiscounts(PromotionDiscountCollection $discounts): void
    {
        $this->discounts = $discounts;
    }

    /**
     * Gets a list of all assigned sales channels for this promotion.
     * Only customers within these channels are allowed
     * to use this promotion.
     */
    public function getSalesChannels(): ?PromotionSalesChannelCollection
    {
        return $this->salesChannels;
    }

    /**
     * Sets a list of permitted sales channels for this promotion.
     * Only customers within these channels are allowed to use this promotion.
     */
    public function setSalesChannels(?PromotionSalesChannelCollection $salesChannels): void
    {
        $this->salesChannels = $salesChannels;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * Gets a list of "order" related rules that need to
     * be valid for this promotion.
     */
    public function getOrderRules(): ?RuleCollection
    {
        return $this->orderRules;
    }

    /**
     * Sets what products are affected by the applied
     * order conditions for this promotion.
     */
    public function setOrderRules(?RuleCollection $orderRules): void
    {
        $this->orderRules = $orderRules;
    }

    /**
     * Gets a list of "persona" related rules that need to
     * be valid for this promotion.
     */
    public function getPersonaRules(): ?RuleCollection
    {
        return $this->personaRules;
    }

    /**
     * Sets what "personas" are allowed
     * to use this promotion.
     */
    public function setPersonaRules(?RuleCollection $personaRules): void
    {
        $this->personaRules = $personaRules;
    }

    /**
     * Gets a list of all customers that have a
     * restricted access due to the explicit assignment
     * within the persona condition settings of the promotion.
     */
    public function getPersonaCustomers(): ?CustomerCollection
    {
        return $this->personaCustomers;
    }

    /**
     * Sets the customers that have explicit access to this promotion.
     * This should be configured within the persona settings of the promotion.
     */
    public function setPersonaCustomers(?CustomerCollection $customers): void
    {
        $this->personaCustomers = $customers;
    }

    /**
     * Gets a list of "cart" related rules that need to
     * be valid for this promotion.
     */
    public function getCartRules(): ?RuleCollection
    {
        return $this->cartRules;
    }

    /**
     * Sets what products are affected by the applied
     * cart conditions for this promotion.
     */
    public function setCartRules(?RuleCollection $cartRules): void
    {
        $this->cartRules = $cartRules;
    }

    /**
     * Gets if the promotion is valid in the current context
     * based on its Persona Rule configuration.
     */
    public function isPersonaConditionValid(SalesChannelContext $context): bool
    {
        /** @var bool $hasRuleRestriction */
        $hasRuleRestriction = $this->getPersonaRules() instanceof RuleCollection && count($this->getPersonaRules()->getElements()) > 0;

        /** @var bool $hasCustomerRestrictions */
        $hasCustomerRestrictions = $this->getPersonaCustomers() instanceof CustomerCollection && count($this->getPersonaCustomers()->getElements()) > 0;

        // check if we even have a restriction
        // otherwise the persona is valid
        if (!$hasRuleRestriction && !$hasCustomerRestrictions) {
            return true;
        }

        // check if we have a list of rules
        // and if any of them is in our current context
        if ($hasRuleRestriction) {
            /** @var string $ruleID */
            foreach ($this->getPersonaRules()->getKeys() as $ruleID) {
                // verify if our persona rule from our promotion
                // is part of our existing rules within the checkout context
                if (in_array($ruleID, $context->getRuleIds(), true)) {
                    // ok at least 1 rule is valid
                    // then this is ok
                    return true;
                }
            }
        }

        // if we are not already valid due to a rule
        // then check if our customer might be assigned directly.
        if ($hasCustomerRestrictions) {
            /** @var CustomerEntity|null $currentCustomer */
            $currentCustomer = $context->getCustomer();

            // check if we have a customer.
            // if we are not logged in, then our restriction is not valid
            // and thus we return false.
            if (!$currentCustomer instanceof CustomerEntity) {
                return false;
            }

            /** @var CustomerCollection|null $customers */
            $customers = $this->getPersonaCustomers();

            // check if our customer ID exists in the keys of permitted customers of the promotion.
            return key_exists($context->getCustomer()->getId(), $customers->getElements());
        }

        // as fallback, always
        // make sure its invalid
        return false;
    }

    /**
     * Gets if the promotion is valid in the current context
     * based on its Cart Condition Rule configuration.
     */
    public function isCartConditionValid(SalesChannelContext $context): bool
    {
        /** @var bool $hasRuleRestriction */
        $hasRuleRestriction = $this->getCartRules() instanceof RuleCollection && count($this->getCartRules()->getElements()) > 0;

        // if there are no rules, the cart is considered as valid
        if (!$hasRuleRestriction) {
            return true;
        }

        // check if we have a list of rules
        // and if any of them is in our current context
        /** @var string $ruleID */
        foreach ($this->getCartRules()->getKeys() as $ruleID) {
            // verify if our cart rules from our promotion
            // is part of our existing rules within the checkout context
            if (in_array($ruleID, $context->getRuleIds(), true)) {
                // ok at least 1 rule is valid
                // then this is ok
                return true;
            }
        }

        return false;
    }

    /**
     * Gets if the promotion is valid in the current context
     * based on its Order Rule configuration.
     */
    public function isOrderConditionValid(SalesChannelContext $context): bool
    {
        /** @var bool $hasRuleRestriction */
        $hasRuleRestriction = $this->getOrderRules() instanceof RuleCollection && count($this->getOrderRules()->getElements()) > 0;

        // check if we even have a restriction
        // otherwise the order condition is valid
        if (!$hasRuleRestriction) {
            return true;
        }

        /** @var string $ruleID */
        foreach ($this->getOrderRules()->getKeys() as $ruleID) {
            // verify if our rule from our promotion
            // is part of our existing rules within the checkout context
            if (in_array($ruleID, $context->getRuleIds(), true)) {
                // ok at least 1 rule is valid
                // then this is ok
                return true;
            }
        }

        // as fallback, always
        // make sure its invalid
        return false;
    }
}
