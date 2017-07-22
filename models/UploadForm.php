<?php


namespace app\models;

use lsolesen\pel\PelEntryWindowsString;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTag;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $imageFiles;
    public $text;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['imageFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => 'jpg', 'maxFiles' => 0],
            [['text'], 'string'],
            [['imageFiles', 'text'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'text' => 'Текст',
            'imageFiles' => 'Загрузка файлов',
        ];
    }

    /**
     * @param array $exifParams
     *
     * @return bool
     * @throws \yii\base\InvalidParamException
     */
    public function upload(array $exifParams = []): bool
    {
        if ($this->validate()) {
            self::cleanUp();
            foreach ($this->imageFiles as $file) {
                $path = 'uploads/' . $file->baseName . '.' . $file->extension;
                $file->saveAs($path);
                $this->addExif($path, $exifParams[$file->baseName]);
            }

            return true;
        }

        return false;
    }

    public function addExif(string $filename, array $exifParams)
    {
        $jpeg = new PelJpeg($filename);
        $title = new PelEntryWindowsString(PelTag::XP_TITLE, $exifParams['title']);
        $subject = new PelEntryWindowsString(PelTag::XP_SUBJECT, $exifParams['subject']);
        $keywords = new PelEntryWindowsString(PelTag::XP_KEYWORDS, $exifParams['keywords']);
        $exif = $jpeg->getExif();
        $tiff = $exif->getTiff();
        $ifd0 = $tiff->getIfd();
        $ifd0->addEntry($title);
        $ifd0->addEntry($subject);
        $ifd0->addEntry($keywords);
        $jpeg->saveFile($filename);
    }

    protected function cleanUp()
    {
        $files = glob('uploads/*.{jpg,tmp}', GLOB_BRACE);
        foreach ($files as $file) {
            unlink($file);
        }
    }
}