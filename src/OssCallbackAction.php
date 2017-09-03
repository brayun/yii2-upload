<?php
/*
 *          ┌─┐       ┌─┐
 *       ┌──┘ ┴───────┘ ┴──┐
 *       │                 │
 *       │       ───       │
 *       │  ─┬┘       └┬─  │
 *       │                 │
 *       │       ─┴─       │
 *       └───┐         ┌───┘
 *           │         └──────────────┐
 *           │                        ├─┐
 *           │                        ┌─┘
 *           │                        │
 *           └─┐  ┐  ┌───────┬──┐  ┌──┘
 *             │ ─┤ ─┤       │ ─┤ ─┤
 *             └──┴──┘       └──┴──┘
 *        @Author Ethan <ethan@brayun.com>
 */

namespace brayun;

use DateTime;
use yii\base\Action;

class OssCallbackAction extends Action
{
    public $domain;

    public function run() {
        $res = \Yii::$app->request->post();
        return $this->controller->asJson(array_merge($res, [
            'url' => $domain.'/'.$res['filename']
        ]));
    }

}
