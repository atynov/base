<div class="image-upload-widget">
    <div id="page-preloader" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;">
        <div style="border: 8px solid #f3f3f3; border-radius: 50%; border-top: 8px solid #3498db; width: 60px; height: 60px; animation: spin 1s linear infinite;"></div>
    </div>
    <label class="btn btn-primary" style="cursor: pointer;">
        Фото қосу
        <?= \yii\helpers\Html::activeFileInput($model, $attribute, [
            'class' => 'form-control-file',
            'accept' => 'image/*',
            'id' => 'image-input',
            'multiple' => true,
            'style' => 'display: none;',
        ]) ?>
    </label>

    <div id="existing-images" style="margin-top: 20px; display: flex; flex-wrap: wrap;">
        <?php foreach ($existingImages as $url): ?>
            <div class="image-wrapper" style="position: relative; margin-right: 10px; margin-bottom: 10px;">
                <img src="<?= $url ?>" style="max-width: 150px; border: 1px solid #ddd; padding: 5px;">
                <span class="remove-image" data-url="<?= $url ?>" style="position: absolute; top: 0; right: 0; background-color: red; color: white; cursor: pointer; padding: 2px 5px; font-size: 16px;">×</span>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Скрытые поля для отправки URL загруженных и удаленных изображений -->
    <input type="hidden" id="uploaded-image-urls" name="uploadedImageUrls" value="<?= implode(',', $existingImages) ?>">
    <input type="hidden" id="deleted-image-urls" name="deletedImageUrls" value="">

    <script>
        document.addEventListener("DOMContentLoaded", function() {
        const imageFiles = []; // Массив для хранения новых выбранных файлов
        let uploadedUrlList = <?= json_encode($existingImages) ?>; // Инициализация с уже загруженными изображениями
        const hiddenInput = document.getElementById('uploaded-image-urls');
        const deletedInput = document.getElementById('deleted-image-urls'); // Поле для удаленных изображений
        const uploadUrl = <?= json_encode($uploadUrl) ?>;
        const preloader = document.getElementById('page-preloader');
        const submitButton = document.getElementById(<?= json_encode($submit) ?>);
        const form = document.getElementById(<?= json_encode($form) ?>);

        // Показываем прелоадер
        function showPreloader() {
            preloader.style.display = 'flex';
        }

        // Скрываем прелоадер
        function hidePreloader() {
            preloader.style.display = 'none';
        }
        hidePreloader()


        // Добавление новых изображений с возможностью удаления
        document.querySelectorAll('.remove-image').forEach(button => {
            button.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                uploadedUrlList = uploadedUrlList.filter(item => item !== url); // Удаляем URL из списка загруженных
                this.parentElement.remove(); // Удаляем изображение из DOM

                // Добавляем URL в список удаленных
                const deletedUrls = deletedInput.value ? deletedInput.value.split(',') : [];
                deletedUrls.push(url);
                deletedInput.value = deletedUrls.join(',');
                hiddenInput.value = uploadedUrlList.join(','); // Обновляем значение скрытого поля
            });
        });

        // Обработка добавления новых изображений
        document.getElementById('image-input').addEventListener('change', function(event) {
            const files = event.target.files;
            for (let i = 0; i < files.length; i++) {
                const fileIndex = imageFiles.length;
                imageFiles.push(files[i]);
                addImagePreview(URL.createObjectURL(files[i]), fileIndex, true); // Добавляем превью
            }
        });

        // Функция для отображения превью изображений с возможностью удаления
        function addImagePreview(url, index, isNew) {
            const previewContainer = document.getElementById('existing-images');
            const imageWrapper = document.createElement('div');
            imageWrapper.classList.add('image-wrapper');
            imageWrapper.style.position = 'relative';
            imageWrapper.style.marginRight = '10px';
            imageWrapper.style.marginBottom = '10px';

            const img = document.createElement('img');
            img.src = url;
            img.style.maxWidth = '150px';
            img.style.border = '1px solid #ddd';
            img.style.padding = '5px';

            const closeBtn = document.createElement('span');
            closeBtn.textContent = '×';
            closeBtn.classList.add('remove-image');
            closeBtn.style.position = 'absolute';
            closeBtn.style.top = '0';
            closeBtn.style.right = '0';
            closeBtn.style.backgroundColor = 'red';
            closeBtn.style.color = 'white';
            closeBtn.style.cursor = 'pointer';
            closeBtn.style.padding = '2px 5px';
            closeBtn.style.fontSize = '16px';

            closeBtn.onclick = function() {
                imageWrapper.remove();
                if (isNew) {
                    delete imageFiles[index]; // Удаляем файл из массива новых файлов
                } else {
                    uploadedUrlList = uploadedUrlList.filter(item => item !== url); // Удаляем URL из массива старых
                    hiddenInput.value = uploadedUrlList.join(','); // Обновляем скрытое поле
                }
            };

            imageWrapper.appendChild(img);
            imageWrapper.appendChild(closeBtn);
            previewContainer.appendChild(imageWrapper);
        }

        submitButton.addEventListener('click', async function(event) {
            event.preventDefault();
            const uploadedUrls = [];
            const validFiles = imageFiles.filter(Boolean);

            for (const file of validFiles) {
                showPreloader();
                const formData = new FormData();
                formData.append('file', file);

                formData.append('<?= Yii::$app->request->csrfParam ?>', '<?= Yii::$app->request->csrfToken ?>');
                const response = await fetch(uploadUrl, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success && data.urls.length > 0) {
                    uploadedUrls.push(data.urls[0]);
                }
                hidePreloader();
            }

            hiddenInput.value = [...uploadedUrlList, ...uploadedUrls].join(',');
            form.submit();
        });
        });
    </script>
</div>
<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>