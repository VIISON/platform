<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Cart\Delivery\Struct;

use Shopware\Core\Framework\Struct\Struct;

class DeliveryInformation extends Struct
{
    /**
     * @var int
     */
    protected $availableStock;

    /**
     * @var float
     */
    protected $weight;

    /**
     * @var DeliveryDate
     */
    protected $inStockDeliveryDate;

    /**
     * @var DeliveryDate
     */
    protected $outOfStockDeliveryDate;

    /**
     * @var bool
     */
    protected $freeDelivery;

    public function __construct(
        int $availableStock,
        float $weight,
        DeliveryDate $inStockDeliveryDate,
        DeliveryDate $outOfStockDeliveryDate,
        bool $freeDelivery
    ) {
        $this->availableStock = $availableStock;
        $this->weight = $weight;
        $this->inStockDeliveryDate = $inStockDeliveryDate;
        $this->outOfStockDeliveryDate = $outOfStockDeliveryDate;
        $this->freeDelivery = $freeDelivery;
    }

    public function getAvailableStock(): int
    {
        return $this->availableStock;
    }

    public function setAvailableStock(int $availableStock): void
    {
        $this->availableStock = $availableStock;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }

    public function getInStockDeliveryDate(): DeliveryDate
    {
        return $this->inStockDeliveryDate;
    }

    public function setInStockDeliveryDate(DeliveryDate $inStockDeliveryDate): void
    {
        $this->inStockDeliveryDate = $inStockDeliveryDate;
    }

    public function getOutOfStockDeliveryDate(): DeliveryDate
    {
        return $this->outOfStockDeliveryDate;
    }

    public function setOutOfStockDeliveryDate(DeliveryDate $outOfStockDeliveryDate): void
    {
        $this->outOfStockDeliveryDate = $outOfStockDeliveryDate;
    }

    public function getFreeDelivery(): bool
    {
        return $this->freeDelivery;
    }

    public function setFreeDelivery(bool $freeDelivery): void
    {
        $this->freeDelivery = $freeDelivery;
    }
}
