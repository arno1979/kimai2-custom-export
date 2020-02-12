<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\CustomExportBundle\Utils;

use App\Constants;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class MPdfConverter implements HtmlToPdfConverter
{
    /**
     * @param string $html
     * @return mixed|string
     * @throws \Mpdf\MpdfException
     */
    public function convertToPdf(string $html)
    {
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
          'orientation' => 'L',
          'fontDir' => array_merge($fontDirs, [
              __DIR__ . '/../ttfonts',
            ]),
          'fontdata' => $fontData + [
              'roboto' => [
                  'R' => 'Roboto-Regular.ttf',
                  'I' => 'Roboto-Italic.ttf'
              ]
          ],
          'default_font' => 'roboto'
          
        ]);

        $mpdf->creator = Constants::SOFTWARE;
        $mpdf->WriteHTML($html);

        return $mpdf->Output('', Destination::STRING_RETURN);
    }
}
