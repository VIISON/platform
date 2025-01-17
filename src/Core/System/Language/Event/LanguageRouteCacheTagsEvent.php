<?php declare(strict_types=1);

namespace Shopware\Core\System\Language\Event;

use Shopware\Core\Framework\Adapter\Cache\StoreApiRouteCacheTagsEvent;
use Shopware\Core\Framework\Log\Package;

#[Package('fundamentals@discovery')]
class LanguageRouteCacheTagsEvent extends StoreApiRouteCacheTagsEvent
{
}
