<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use RepeatBot\Core\ORM\Collections\TrainingCollection;
use RepeatBot\Core\ORM\Entities\Export;
use RepeatBot\Core\ORM\Repositories\ExportRepository;
use RepeatBot\Core\ORM\Repositories\TrainingRepository;

/**
 * Class ExportService
 * @package RepeatBot\Bot\Service
 */
class ExportQueueService
{
    /**
     * OneYearService constructor.
     *
     * @param TrainingRepository $training
     * @param ExportRepository $export
     */
    public function __construct(private TrainingRepository $training, private ExportRepository $export)
    {
    }

    /**
     * @param Export $export
     *
     * @throws TelegramException
     * @throws MpdfException
     */
    public function execute(Export $export): void
    {
        $array = explode('_', $export->getWordType());
        if (
            count($array) == 2 &&
            in_array($array[0], ['FromEnglish', 'ToEnglish']) &&
            in_array($array[1], ['first', 'second', 'third', 'fourth', 'fifth', 'sixth', 'never'])
        ) {
            $trainings = $this->training->getTrainingsWithStatus(
                $export->getUserId(),
                $array[0],
                $array[1]
            );
        } else {
            $trainings = $this->training->getTrainings($export->getUserId(), 'FromEnglish');
        }
        $uri  = $this->createExportFile($trainings, $export);
        $data = [
            'chat_id'                  => $export->getChatId(),
            'text'                     => 'export',
            'document'                 => Request::encodeFile($uri),
            'caption'                  => 'Експорт доданих слів',
            'disable_web_page_preview' => true,
            'disable_notification'     => 1,
        ];
        Request::sendDocument($data);
        $this->export->applyExport($export);
    }

    /**
     * @param TrainingCollection $trainings
     * @param Export $export
     *
     * @return string
     * @throws MpdfException
     */
    private function createExportFile(TrainingCollection $trainings, Export $export): string
    {
        $mpdf       = new Mpdf(['tempDir' => '/tmp']);
        $stylesheet = file_get_contents('/app/resource/export.css');
        $mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);
        $header = '<table width="100%"><tr>';
        $header .= '<td width="50%" style="text-align: left; font-weight: bold;">Експорт доданих слів</td>';
        $header .= '<td width="50%" style="text-align: right; font-weight: bold;">';
        $header .= '<a href="https:/t.me/RepeatWordBot">Telegram @RepeatWordBot</a></td></tr></table>';
        $footer = '<table width="100%"><tr>';
        $footer .= '<td width="33%"></td>';
        $footer .= '<td width="33%" align="center">{PAGENO}/{nbpg}</td>';
        $footer .= '<td width="33%" style="text-align: right;"></td>';
        $footer .= '</tr></table>';
        $mpdf->SetHTMLHeader($header);
        $mpdf->SetHTMLFooter($footer);

        $mpdf->WriteHTML('<table id="export"><tr><th>№ п.п.</th><th>Слово</th><th>Переклад</th></tr>');
        foreach ($trainings as $id => $training) {
            $mpdf->WriteHTML(
                strtr("<tr><td>:number</td><td>:word</td><td>:translate</td></tr>", [
                    ':number'    => ((int)$id) + 1,
                    ':word'      => $training->getWord()->getWord(),
                    ':translate' => $training->getWord()->getTranslate(),
                ])
            );
        }
        $mpdf->WriteHTML('</table>');
        $uri = '/tmp/' . $export->getUserId() . '.pdf';
        $mpdf->Output($uri, 'F');

        return $uri;
    }
}
