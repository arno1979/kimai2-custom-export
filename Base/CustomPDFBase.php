<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\CustomExportBundle\Base;

use App\Export\ExportItemInterface;
use App\Repository\Query\TimesheetQuery;
use App\Timesheet\UserDateTimeFactory;
use KimaiPlugin\CustomExportBundle\Utils\HtmlToPdfConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Twig\Environment;

class CustomPDFBase
{
    use \App\Export\Renderer\RendererTrait;

    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var UserDateTimeFactory
     */
    private $dateTime;
    /**
     * @var HtmlToPdfConverter
     */
    private $converter;

    public function __construct(Environment $twig, UserDateTimeFactory $dateTime, HtmlToPdfConverter $converter)
    {
        $this->twig = $twig;
        $this->dateTime = $dateTime;
        $this->converter = $converter;
    }

    protected function getTemplate(): string
    {
        return '@CustomExport/export/ascustompdf.html.twig';
    }

    /**
     * @param ExportItemInterface[] $timesheets
     * @param TimesheetQuery $query
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render(array $timesheets, TimesheetQuery $query): Response
    {
        $content = $this->twig->render($this->getTemplate(), [
            'entries' => $timesheets,
            'query' => $query,
            'now' => $this->dateTime->createDateTime(),
            'summaries' => $this->calculateSummary($timesheets),
        ]);

        $content = $this->converter->convertToPdf($content);

        $response = new Response($content);

        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'kimai-export.pdf');

        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    public function getId(): string
    {
        return 'ascustompdf';
    }
}
