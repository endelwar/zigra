<?php

require_once __DIR__ . '/Zigra/Core.php';
/**
 * This class only exists for backwards compatability. All code was moved to
 * Zigra_Core and this class extends Zigra_Core
 *
 * @author  Manuel Dalla Lana <endelwar@aregar.it>
 * @license Proprietary
 * @since   0.1.0
 */
class Zigra extends Zigra_Core
{
    public const VERSION = '0.8.8';
}
