<?php

namespace backend\controllers;

use common\models\Order;
use common\models\OrdersProduct;
use common\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'forgot-password', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $totalEarnings = Order::find()->paid()->sum('total_price');
        $totalOrders = Order::find()->paid()->count();
        $totalProducts = OrdersProduct::find()
            ->alias('ps')
            ->innerJoin(Order::tableName() . 'p', 'p.id=ps.order_id')
            ->andWhere(['p.status' => [Order::STATUS_PAID, Order::STATUS_COMPLETED]])
            ->sum('quantity');
        $totalUsers = User::find()->andWhere(['status' => User::STATUS_ACTIVE])->count();


        //armazenar os dados por dia por que nao temos dados suficientes para ser por mes ou por ano, no futuro modificar

        $orders = Order::findBySql("SELECT
                                CAST(DATE_FORMAT(FROM_UNIXTIME(p.created_at), '%Y-%m-%d %H:%i:%s') as DATE) as `date`,
                                SUM(p.total_price) as `total_price`
                                FROM orders p
                                WHERE p.status IN (" . Order::STATUS_PAID . ", " . Order::STATUS_COMPLETED . ")
                                GROUP BY CAST(DATE_FORMAT(FROM_UNIXTIME(p.created_at), '%Y-%m-%d %H:%i:%s') as DATE)
                                ORDER BY p.created_at")
            ->asArray()
            ->all();

        //Here to set the line chart
        $earningsN = [];
        $labels = [];
        if (!empty($orders)) {
            $dataMin = $orders[0]['date'];
            $orderByPriceUsingMap = ArrayHelper::map($orders, 'date', 'total_price');
            $dm = new \DateTime($dataMin);
            $actualDate = new \DateTime();
            $dates = [];

            while ($dm->getTimestamp() < $actualDate->getTimestamp()) {
                $labels = $dm->format('d/m/Y');
                $labels[] = $labels;
                $earningsN = (float)$orderByPriceUsingMap[$dm->format('Y-m-d')] ?? 0;
                $dm->setTimestamp($dm->getTimestamp() + 86400);

            }


        }
        return $this->render('index', [
            'totalEarnings' => $totalEarnings,
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'totalUsers' => $totalUsers,
            'data' => $earningsN,
            'labels' => $labels
        ]);
    }
    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
    //funcao para
    public function actionForgotPassword()
    {
        return " ";

    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
