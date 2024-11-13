<?php
use yii\helpers\Html;
use modules\directory\enums\DicValueTypeEnum;

/* @var $this yii\web\View */
/* @var $items app\models\DicValues[] */

$this->title = 'Аумақтар';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css');
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');



$treeData = [];
foreach ($items as $item) {
    $icon = '';
    switch ($item->type) {
        case DicValueTypeEnum::REGION:
            $icon = 'fas fa-map-marker-alt'; // Иконка для области
            break;
        case DicValueTypeEnum::DISTRICT:
            $icon = 'fas fa-map-signs'; // Иконка для района
            break;
        case DicValueTypeEnum::CITY:
            $icon = 'fas fa-city'; // Иконка для города
            break;
    }

    $treeData[] = [
        'id' => (string)$item->id,
        'parent' => $item->parent_id ? (string)$item->parent_id : '#', // Используем '#' для корневых элементов
        'text' => $item->name['kk'],
        'icon' => $icon,
        'li_attr' => ['data-type' => $item->type],
    ];
}


$treeDataJson = json_encode($treeData);
?>

<p><?= Html::a('<i class="fas fa-plus"></i> Жазбаны қосу', ['create'], ['class' => 'btn btn-success']) ?></p>

<div id="jstree-container" class="mt-3"></div>

<?php
$this->registerJs("
    $('#jstree-container').jstree({
        'core': {
            'data': $treeDataJson,
            'check_callback': true
        },
        'plugins': ['contextmenu', 'dnd', 'types', 'state', 'wholerow'],
        'types': {
            'default': {
                'icon': 'jstree-icon jstree-file'
            }
        },
        'contextmenu': {
            'items': function(node) {
                return {
                    'edit': {
                        'label': 'Өзгерту',
                        'icon': 'fas fa-edit',
                        'action': function() {
                            window.location.href = 'default/update?id=' + node.id;
                        }
                    },
                    'delete': {
                        'label': 'Жою',
                        'icon': 'fas fa-trash-alt',
                        'action': function() {
                            if (confirm('Бұл элементті жоюға сенімдісіз бе?')) {
                                window.location.href = 'default/delete?id=' + node.id;
                            }
                        }
                    }
                };
            }
        }
    });

    $('#jstree-container').on('select_node.jstree', function (e, data) {
        var nodeId = data.node.id;
        var actions = `<a href='default/update?id=`+nodeId+`' class='btn btn-primary'><i class='fas fa-edit'></i> Өзгерту</a>
                       <a href='default/delete?id=`+nodeId+`' class='btn btn-danger' data-confirm='Бұл элементті жоюға сенімдісіз бе?' data-method='post'><i class='fas fa-trash-alt'></i> Жою</a>`;
        $('#actions').html(actions);
    });
");
?>

<div id="actions" style="margin-top: 20px;"></div>

