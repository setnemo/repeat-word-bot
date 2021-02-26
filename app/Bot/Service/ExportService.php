<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service;

use Longman\TelegramBot\Request;
use Mpdf\Mpdf;
use RepeatBot\Core\Database\Model\Export;
use RepeatBot\Core\Database\Repository\ExportRepository;
use RepeatBot\Core\Database\Repository\TrainingRepository;

/**
 * Class ExportService
 * @package RepeatBot\Bot\Service
 */
class ExportService
{
    /**
     * OneYearService constructor.
     *
     * @param TrainingRepository $trainingRepository
     */
    public function __construct(
        private TrainingRepository $trainingRepository,
        private ExportRepository $exportRepository
    ){
    }

    /**
     * @param int $id
     */
    public function execute(Export $export): void
    {
        $array = explode('_', $export->getWordType());
        if (
            count($array) == 2 &&
            in_array($array[0], ['FromEnglish','ToEnglish']) &&
            in_array($array[1], ['first','second','third','fourth','fifth','sixth','never'])
        ) {
            $trainings = $this->trainingRepository->getTrainingsWithStatus(
                $export->getUserId(),
                $array[0],
                $array[1]
            );
        } else {
            $trainings = $this->trainingRepository->getTrainings($export->getUserId(), 'FromEnglish');
        }
        $uri = $this->createExportFile($trainings, $export);
        $data = [
            'chat_id' => $export->getChatId(),
            'text' => 'export',
            'document' => Request::encodeFile($uri),
            'caption' => 'Экспорт изучаемых слов',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ];
        Request::sendDocument($data);
        $this->exportRepository->applyExport($export);
    }
    
    /**
     * @param array  $trainings
     * @param Export $export
     *
     * @return string
     * @throws \Mpdf\MpdfException
     */
    private function createExportFile(array $trainings, Export $export): string
    {
        $mpdf = new Mpdf(['tempDir' => '/tmp']);
        $stylesheet = file_get_contents('/app/resource/export.css');
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->SetHTMLHeader('<table width="100%"><tr>' .
            '<td width="50%" style="text-align: left; font-weight: bold;">Export learning words</td>' .
            '<td width="50%" style="text-align: right; font-weight: bold;"><a href="https:/t.me/RepeatWordBot">Telegram @RepeatWordBot</a></td>' .
            '</tr></table>');
        $mpdf->SetHTMLFooter('<table width="100%"><tr>' .
            '<td width="33%"></td>' .
            '<td width="33%" align="center">{PAGENO}/{nbpg}</td>' .
            '<td width="33%" style="text-align: right;"></td>' .
            '</tr></table>');
        
        $mpdf->WriteHTML('<table id="export">');
        $mpdf->WriteHTML('<tr><th>№ п.п.</th><th>Слово</th><th>Перевод</th></tr>');
        foreach ($trainings as $id => $training) {
            $mpdf->WriteHTML(strtr("<tr><td>:number</td><td>:word</td><td>:translate</td></tr>", [
                ':number' => $id + 1,
                ':word' => $training->getWord(),
                ':translate' => $training->getTranslate(),
            ]));
        }
        $mpdf->WriteHTML('</table>');
        $uri = '/tmp/' . $export->getUserId() . '.pdf';
        $mpdf->Output($uri, 'F');
        
        return $uri;
}
}
