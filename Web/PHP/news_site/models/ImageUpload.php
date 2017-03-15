<?php

namespace app\models;

use yii\base\model;
use yii\web\UploadedFile;
use Yii;

class ImageUpload extends Model
{
    public $statusActive;
    public $image;

    public function rules()
    {
        return [
            [['statusActive'], 'string'],
            [['image'], 'required', 'when' => function ($model) {
                return $model->statusActive == '1';
            }, 'whenClient' => "function(attribute, value) {
                    return $('#statusActive').val() == '1';
                }",],
            [['image'], 'file', 'extensions' => 'jpg,png']
        ];
    }

    public function attributeLabels()
    {
        return [
            'image' => 'Картинка',
        ];
    }

    /**
     * Загружаем новую картинку и удаляем старую при необходимости
     * @param UploadedFile $file
     * @param string $currentImage
     * @return string
     */
    public function uploadFile(UploadedFile $file, $currentImage)
    {
        $this->image = $file;

        if ($this->validate()) {
            $this->deleteCurrentImage($currentImage);
            return $this->saveImage();
        }
    }

    /**
     * Получаем путь, куда необходимо сохранить картинку
     * @return string
     */
    private function getFolder()
    {
        return Yii::getAlias('@web') . 'uploads/';
    }

    /**
     * Генерируем уникальное имя картинки, какая записана в свойстве image, текущего класса
     * @return string
     */
    private function generateFileName()
    {
        // $this->image->baseName - получаем имя файла без расширения
        // uniqid($this->image->baseName) - генерируем уникальный id и вставляем в конец имени файла
        // md5(uniqid($this->image->baseName)) - зашифровуем это имя в md5, чтобы не получилось одинаковое имя файла
        // $this->image->extension - получаем расширение переданного файла
        // strtolower(md5(uniqid($this->image->baseName)) . '.' . $this->image->extension) - переводим всё в нижний регистр
        return strtolower(md5(uniqid($this->image->baseName)) . '.' . $this->image->extension);
    }

    /**
     * Удаляем старую картинку, если она существует
     * @param string $currentImage Имя картинки
     */
    public function deleteCurrentImage($currentImage)
    {
        if ($this->fileExists($currentImage)) unlink($this->getFolder() . $currentImage);
    }

    /**
     * Проверяем указанный файл на существование
     * @param string $currentImage Имя картинки
     * @return bool Если это файл и он существует возвращаем true
     */
    public function fileExists($currentImage)
    {
        if (!empty($currentImage) && $currentImage != null) {
            return is_file($this->getFolder() . $currentImage) && file_exists($this->getFolder() . $currentImage);
        }
    }

    /**
     * Сохраняем картинку под указанным путем проекта
     * @return string
     */
    public function saveImage()
    {
        $fileName = $this->generateFileName();

        $this->image->saveAs($this->getFolder() . $fileName);

        return $fileName;
    }
}