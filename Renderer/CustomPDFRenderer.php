<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\CustomExportBundle\Renderer;

use App\Entity\ExportableItem;
use App\Export\ExportFilename;
use App\Export\ExportRendererInterface;
use App\Entity\Timesheet;
use App\Entity\ProjectMeta;
use App\Timesheet\DateTimeFactory;
use App\Pdf\HtmlToPdfConverter;
use App\Pdf\PdfContext;
use App\Pdf\PdfRendererTrait;
use App\Project\ProjectStatisticService;
use App\Export\RendererInterface;
use App\Export\Base\RendererTrait;
use App\Repository\Query\TimesheetQuery;
use App\Repository\Query\ProjectQuery;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Doctrine\DBAL\Connection;
use KimaiPlugin\CustomExportBundle\EventSubscriber\MetaFieldDisplaySubscriber;


final class CustomPDFRenderer implements RendererInterface
{
    use RendererTrait;
    use PDFRendererTrait;

    private string $id = 'ascustompdf';
    private string $template = 'custom-export.pdf.twig';
    private array $pdfOptions = [];

    public function __construct(Environment $twig, DateTimeFactory $dateTime, HtmlToPdfConverter $converter, Connection $conn, private ProjectStatisticService $projectStatisticService)
    {
        $this->twig = $twig;
        $this->dateTime = $dateTime;
        $this->converter = $converter;
        $this->conn = $conn;
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

        $context = new PdfContext();
        $filename = new ExportFilename($query);
        $context->setOption('filename', $filename->getFilename());

        $customFilename = $query->getProjects()[0]->getMetaField('custom_export_filename');
        if ($query->getProjects()[0]->getMetaField('custom_export_filename') !== null && $query->getProjects()[0]->getMetaField('custom_export_filename')->getValue() !== null)
        {
          $filename = $query->getProjects()[0]->getMetaField('custom_export_filename')->getValue().$query->getBegin()->format('Y-m');
          $context->setOption('filename', $filename);
        }

        $summary = $this->calculateSummary($timesheets);
        $content = $this->twig->render($this->getTemplate(), array_merge([
            'entries' => $timesheets,
            'query' => $query,
            'now' => $this->dateTime->createDateTime(),
            'summaries' => $summary,
            'budgets' => $this->calculateProjectBudget($timesheets, $query, $this->projectStatisticService),
            'decimal' => false,
            'pdfContext' => $context,
            'projectRate' => $this->getRate($query),
            'filename' => $filename,
        ], $this->getOptions($query)));

        $pdfOptions = array_merge($context->getOptions(), $this->getPdfOptions());

        $content = $this->converter->convertToPdf($content, $pdfOptions);

        return $this->createPdfResponse($content, $context);

    }

    public function getId(): string
    {
        return 'ascustompdf';
    }

    public function getIcon(): string
    {
        return 'pdf-ascustom';
    }

    public function getTitle(): string
    {
        return 'pdf-ascustom';
    }

    protected function getTemplate(): string
    {
      return '@CustomExport/export/' . $this->template;
    }

    protected function getOptions(TimesheetQuery $query): array
    {
        $decimal = false;
        if (null !== $query->getCurrentUser()) {
            $decimal = $query->getCurrentUser()->isExportDecimal();
        } elseif (null !== $query->getUser()) {
            $decimal = $query->getUser()->isExportDecimal();
        }

        return ['decimal' => $decimal];
    }

    public function getPdfOptions(): array
    {
        return $this->pdfOptions;
    }

    public function setPdfOption(string $key, string $value): CustomPDFRenderer
    {
        $this->pdfOptions[$key] = $value;

        return $this;
    }

    protected function getRate(TimesheetQuery $query)
    {
        $queryBuilder = $this->conn->createQueryBuilder();
        $data = $queryBuilder->select('rate')->from('kimai2_projects_rates')->where('project_id = '.$query->getProjectIds()[0].' and user_id = '.$query->getCurrentUser()->getId())->execute()->fetchAll();
        $projectRate = $data[0]['rate'] ? $data[0]['rate'] : '';
        return $projectRate;
    }

}
