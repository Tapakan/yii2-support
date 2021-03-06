<?php

namespace hexaua\yiisupport;

use hexaua\yiisupport\events\CommentEvent;
use hexaua\yiisupport\events\TicketEvent;
use hexaua\yiisupport\helpers\Config;
use hexaua\yiisupport\interfaces\ConfigInterface;
use hexaua\yiisupport\models\Comment;
use hexaua\yiisupport\models\Ticket;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\base\Module as BaseModule;
use yii\console\Application;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Url;

/**
 * Class Module
 */
class Module extends BaseModule implements BootstrapInterface
{
    const EVENT_AFTER_COMMENT_CREATE = 'afterCommentCreate';
    const EVENT_AFTER_TICKET_CREATE = 'afterTicketCreate';

    /**
     * @var string Admin role
     */
    public $adminRole;

    /**
     * @var string User role
     */
    public $userRole;

    /**
     * @var string
     */
    public $showTitle;

    /**
     * @var string
     */
    public $authorNameTemplate;

    /**
     * @var string
     */
    public $uploadDir = '@webroot/uploads/support';

    /**
     * Url to upload folder.
     *
     * @var string
     */
    public $uploadUrl = '@web/uploads/support';

    /**
     * On/off action buttons for Ticket.
     *
     * @var array
     */
    public $buttons
        = [
            'delete' => true,
            'update' => true,
            'resolve' => true,
        ];

    /**
     * List of accepted extensions.
     *
     * @var array
     */
    public $extensions
        = [
            'png',
            'jpg',
            'jpeg',
            'pdf',
            'doc',
            'docx',
            'ppt',
            'pptx',
            'xls',
            'xlsx'
        ];

    /**
     * List of accepted mime types.
     *
     * @var array
     */
    public $mimeTypes
        = [
            'png' => 'image/png',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

    /**
     * @inheritdoc
     * @var string
     */
    public $version = '1.0.0';

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, $config = [])
    {
        $config = ArrayHelper::merge(
            require(__DIR__ . '/config/main.php'),
            $config
        );

        parent::__construct($id, $parent, $config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        \Yii::setAlias('@yiisupport', __DIR__);
        \Yii::$container->setSingleton(ConfigInterface::class, Config::class,
            [$this]);

        if (!isset(\Yii::$app->getI18n()->translations['support'])) {
            \Yii::$app->getI18n()->translations['support'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@yiisupport/messages'
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $app->getUrlManager()->addRules(
            require(__DIR__ . '/config/routes.php'),
            false
        );

        if ($app instanceof Application) {
            $app->controllerMap[$this->id] = [
                'class' => 'hexaua\yiisupport\console\RbacController',
                'module' => $this,
            ];
        }
    }

    /**
     * Retrieves value from module "params".
     *
     * @param string|int $key
     * @param mixed      $default
     *
     * @return mixed
     */
    public function param($key, $default = null)
    {
        return ArrayHelper::getValue($this->params, $key, $default);
    }

    /**
     * Return sub folder for current user.
     *
     * @return int|string
     */
    public function getOwnerPath()
    {
        return Yii::$app->user->isGuest ? 'guest' : Yii::$app->user->id;
    }

    /**
     * Returns path to the upload dir.
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function getSaveDir()
    {
        $path = Yii::getAlias($this->uploadDir);
        if (FileHelper::createDirectory($path, 0777) && file_exists($path)) {
            return $path;
        }

        throw new InvalidConfigException('Invalid config $uploadDir');
    }

    /**
     * @param string $name
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function getPath($name)
    {
        return $this->getSaveDir() . DIRECTORY_SEPARATOR . $name;
    }

    /**
     * Return url to file.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getUrl($name)
    {
        if (is_callable($this->uploadUrl)) {
            return call_user_func($this->uploadUrl, $name);
        }

        return Url::to($this->uploadUrl . '/' . $name);
    }

    /**
     * Triggers event after comment create.
     *
     * @param Comment $model
     */
    public function onCommentCreate(Comment $model)
    {
        $this->trigger(static::EVENT_AFTER_COMMENT_CREATE,
            new CommentEvent(['sender' => $model]));
    }

    /**
     * Triggers event after ticket create.
     *
     * @param Ticket $model
     */
    public function onTicketCreate(Ticket $model)
    {
        $this->trigger(
            static::EVENT_AFTER_TICKET_CREATE,
            new TicketEvent(['sender' => $model])
        );
    }
}
