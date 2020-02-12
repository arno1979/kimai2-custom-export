<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\CustomExportBundle\Renderer;

use KimaiPlugin\CustomExportBundle\Base\CustomPDFBase as BasePDFRenderer;
use App\Export\RendererInterface;

final class CustomPDFRenderer extends BasePDFRenderer implements RendererInterface
{
    public function getIcon(): string
    {
        return 'pdf';
    }

    public function getTitle(): string
    {
        return 'pdf-ascustom';
    }
}
