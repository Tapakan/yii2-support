<?php
/**
 * CreateAction
 * @version     1.0
 * @license     http://mit-license.org/
 * @author      Tapakan https://github.com/Tapakan
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 * @copyright   Copyright (C) Hexa,  All rights reserved.
 */

namespace hexaua\yiisupport\actions;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class CreateAction
 */
class CreateAction extends BaseAction
{
    /**
     * @return mixed
     */
    public function run()
    {
        /** @var ActiveRecord $model */
        $model = new $this->modelClass();

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([$this->controller->getUniqueId() . '/view', 'id' => $model->id]);
        }

        return $this->controller->render('create', ArrayHelper::merge([
            'model' => $model,
        ], (array)$this->params));
    }
}
