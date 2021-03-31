<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<!--todos as funcoes dos produtos estao aqui a forma que ele vai aprecer imagem nome id,update-->
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
<!--        vai para criar um novo produto-->
        <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<!--    A image vai requerer mais trabalho visto que teremos que pegar a imagem e configurar para ela aparecer nesse view -->
    <div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                    'attribute' => 'id',
                    'contentOptions' => [
                            'style' => 'width: 55px'
                    ]
            ],
//            configurando a imagem e mostrando o caminho de onde a imagem vai vir na funcao getImage
            [
                'label' => 'Image',
                'attribute' => 'image',
                'content' => function($model){
                    /**  @var  \common\models\Product $model */
                    return Html::img($model ->getImageUrl(), ['style'=> 'width:50px']);
                }
            ],
            'name',

            'price:currency',
//            make a nice way to see the status, that shhow if the procuct is active at the web or not
            [
                    'attribute'=> 'status',
                    'content'=> function($model){
                        /**  @var  \common\models\Product $model */
                        return Html::tag('span', $model->status ? 'Active' : 'Draft',[
                                'class' => $model->status ? 'badge badge-success' : 'badge badge-danger']);

                    }
            ],
//            created and update way to show at the screen make it in one line, formatter in config\main
            [
                'attribute'=> 'created_at',
                'format' => ['datetime'],
                'contentOptions' => ['style' => 'white-space: nowrap']
            ],
            [
                'attribute'=> 'updated_at',
                'format' => ['datetime'],
                'contentOptions' => ['style' => 'white-space: nowrap']
            ],
            //'created_by',
            //'updated_by',

//os botoes que vao aparecer criado no common/grid AcionColumn-> que vai permitir usar as funcionalidades do bootstrap4
            [
                'class' => 'common\grid\ActionColumn',
                'template'=>'{view} {update} {delete}'
            ],
        ],
    ]); ?>
    </div>

</div>
