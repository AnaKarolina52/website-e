<?php


namespace frontend\controllers;


use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class ProfileController extends \frontend\base\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'update-location', 'update-account'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]

                ],
            ],
        ];
    }


    public function actionIndex()
    {
        /** @var \common\models\User $user */
        $user = Yii::$app->user->identity;
        //$userLocations =$user->locations;
        $userLocation = $user->getLocation();
        //$userLocation->user_id = $user->id;
        return $this->render('index', [
            'user' => $user,
            'userLocation' => $userLocation

        ]);
    }

    public function actionUpdateLocation()
    {
        if(!Yii::$app->request->isAjax){
            throw new ForbiddenHttpException("Just Ajax request is allowed ");
        }
        $user = Yii::$app->user->identity;
        $userLocation = $user->getLocation();
        $success = false;
        if ($userLocation->load(Yii::$app->request->post()) && $userLocation->save()) {
            $success = true;
        }
        return $this->renderAjax('user_location', [
            'userLocation' => $userLocation,
            'success' => $success
        ]);
    }

    public function actionUpdateAccount()
    {
        if(!Yii::$app->request->isAjax){
           throw new ForbiddenHttpException("Just Ajax request is allowed ");
        }
        $user = Yii::$app->user->identity;
        $success = false;
        if ($user->load(Yii::$app->request->post()) && $user->save()) {
            $success = true;
        }
        return $this->renderAjax('user_account', [
            'user' => $user,
            'success' => $success
        ]);
    }

}