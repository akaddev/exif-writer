<?php
/* @var $this yii\web\View */

/* @var $model \app\models\UploadForm */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

<?= $form->field($model, 'imageFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/*'])->label('Загрузка файлов', ['class' => 'btn btn-block btn-primary']) ?>
<?= $form->field($model, 'text')->textarea() ?>

<?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>
