<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\CustomExportBundle\Renderer;

use KimaiPlugin\CustomExportBundle\Base\CustomHtmlBase as BaseHtmlRenderer;
use App\Export\RendererInterface;


final class CustomHtmlRenderer extends BaseHtmlRenderer implements RendererInterface
{
    public function getIcon(): string
    {
        return 'print';
    }

    public function getTitle(): string
    {
        return 'print-ascustom';
    }
}
