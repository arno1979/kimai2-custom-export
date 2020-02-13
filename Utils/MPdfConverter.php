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

    private $cacheDirectory;

    public function __construct(string $cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;
    }

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
          'tempDir' => $this->cacheDirectory,
          'orientation' => 'L',
          'fontDir' => array_merge($fontDirs, [
              __DIR__ . '/../ttfonts',
            ]),
          'fontdata' => $fontData + [
              'roboto' => [
                  'R' => 'Roboto-Regular.ttf',
                  'B' => 'Roboto-Bold.ttf',
                  'I' => 'Roboto-Italic.ttf',
                  'BI' => 'Roboto-BoldItalic.ttf'
              ],
              'robotoslab' => [
                  'R' => 'RobotoSlab-VariableFont.ttf',
                  'B' => 'RobotoSlab-Bold.ttf'
              ]
              
          ],
          'default_font' => 'roboto'
          
        ]);

        $mpdf->creator = Constants::SOFTWARE;
        $mpdf->WriteHTML($html);

        return $mpdf->Output('', Destination::STRING_RETURN);
    }
}
