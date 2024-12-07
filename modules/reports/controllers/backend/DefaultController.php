<?php

namespace modules\reports\controllers\backend;

use modules\organization\models\Organization;
use modules\reports\models\Direction;
use modules\reports\models\Report;
use modules\reports\models\search\ReportSearch;
use Yii;
use backend\controllers\BackendController;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements actions for reports model.
 */
class DefaultController extends BackendController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new ReportSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'directions' => Direction::getList(),
            'organizations' => Organization::getOrganizationsList(),
        ]);
    }

    public function actionExport($type, $ids)
    {
        // Получаем выбранные отчеты
        $ids = explode(',', $ids);
        $reports = Report::find()
            ->where(['id' => $ids])
            ->with(['organization', 'direction'])
            ->all();

        if ($type === 'word') {
            return $this->exportWord($reports);
        } else {
            return $this->exportPdf($reports);
        }
    }

    protected function exportWord($reports)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();

        // Добавляем заголовок
        $section->addText('Есептер тізімі', ['bold' => true, 'size' => 16]);

        foreach ($reports as $report) {
            $section->addText('Шара атауы: ' . $report->name);
            $section->addText('Ұйым: ' . $report->organization->name['kk']);
            $section->addText('Мерзімі: ' . Yii::$app->formatter->asDate($report->start_date));
            $section->addText('Қамтылған адам саны: ' . $report->people_count);
            $section->addTextBreak();
        }

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment;filename="reports.docx"');
        $writer->save('php://output');
        exit;
    }

    protected function exportPdf($reports)
    {
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Установка шрифта с поддержкой кириллицы
        $pdf->SetFont('dejavusans', '', 12);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Есептер тізімі');

        // Установка языка документа
        $pdf->setLanguageArray(['kk' => 'Kazakh']);

        $pdf->AddPage();

        $html = '<h1>Есептер тізімі</h1>';
        foreach ($reports as $report) {
            $html .= '<p><strong>Шара атауы:</strong> ' . $report->name . '</p>';
            $html .= '<p><strong>Ұйым:</strong> ' . $report->organization->name['kk'] . '</p>';
            $html .= '<p><strong>Мерзімі:</strong> ' . Yii::$app->formatter->asDate($report->start_date) . '</p>';
            $html .= '<p><strong>Қамтылған адам саны:</strong> ' . $report->people_count . '</p><hr>';
        }

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output('reports.pdf', 'D');
        exit;
    }




    /**
     * Отображает одну запись отчета.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException если отчет не найден
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }



    /**
     * Удаляет существующий отчет.
     * Если удаление успешно, перенаправляет на страницу списка.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException если отчет не найден
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Находит модель отчета по первичному ключу.
     * Если модель не найдена, выбрасывает исключение 404.
     * @param integer $id
     * @return Report найденная модель
     * @throws NotFoundHttpException если модель не найдена
     */
    protected function findModel($id)
    {
        if (($model = Report::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемый отчет не найден.');
    }
}
