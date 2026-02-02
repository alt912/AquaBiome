<?php

namespace App\Controller;

use App\Form\ExcelType;
use AppBundle\Form\Type\ExcelFormatType;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;

#[Route('/excel')]
final class ExcelController extends AbstractController
{
    #[Route('/export', name: 'app_excel_export')]
    public function export(Request $request): Response
    {
        $form = $this->createForm(ExcelType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $format = $data['format'];
            $filename = 'Browser_characteristics.' . $format;

            $spreadsheet = $this->createSpreadsheet();

            switch ($format) {
                case 'ods':
                    $contentType = 'application/vnd.oasis.opendocument.spreadsheet';
                    $writer = new Ods($spreadsheet);
                    break;
                case 'xlsx':
                    $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                    $writer = new Xlsx($spreadsheet);
                    break;
                case 'csv':
                    $contentType = 'text/csv';
                    $writer = new Csv($spreadsheet);
                    break;
                default:
                    return $this->render('excel/index.html.twig', [
                        'form' => $form->createView(),
                    ]);
            }

            $response = new StreamedResponse();
            $response->headers->set('Content-Type', $contentType);
            $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
            $response->setPrivate();
            $response->headers->addCacheControlDirective('no-cache', true);
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->setCallback(function () use ($writer) {
                $writer->save('php://output');
            });

            return $response;
        }
        return $this->render('excel/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    protected function createSpreadsheet()
    {
        $spreadsheet = new Spreadsheet();
        // Get active sheet - it is also possible to retrieve a specific sheet
        $sheet = $spreadsheet->getActiveSheet();

        // Set cell name and merge cells
        $sheet
            ->setCellValue('A1', 'Browser characteristics')
            ->mergeCells('A1:D1');

        // Set column names
        $columnNames = [
            'Browser',
            'Developper',
            'Release date',
            'Written in',
        ];
        $columnLetter = 'A';
        foreach ($columnNames as $columnName) {
            // Allow to access AA column if needed and more
            $columnLetter++;
            $sheet->setCellValue($columnLetter . '2', $columnName);
        }

        // Add data for each column
        $columnValues = [
            ['Google Chrome', 'Google Inc.', 'September 2, 2008', 'C++'],
            ['Firefox', 'Mozilla Foundation', 'September 23, 2002', 'C++, JavaScript, C, HTML, Rust'],
            ['Microsoft Edge', 'Microsoft', 'July 29, 2015', 'C++'],
            ['Safari', 'Apple', 'January 7, 2003', 'C++, Objective-C'],
            ['Opera', 'Opera Software', '1994', 'C++'],
            ['Maxthon', 'Maxthon International Ltd', 'July 23, 2007', 'C++'],
            ['Flock', 'Flock Inc.', '2005', 'C++, XML, XBL, JavaScript'],
        ];

        $i = 3; // Beginning row for active sheet
        foreach ($columnValues as $columnValue) {
            $columnLetter = 'A';
            foreach ($columnValue as $value) {
                $columnLetter++;
                $sheet->setCellValue($columnLetter . $i, $value);
            }
            $i++;
        }

        return $spreadsheet;
    }
}
