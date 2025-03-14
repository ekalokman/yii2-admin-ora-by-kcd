<?php

namespace ekalokman\AdminOci8\controllers;

use Yii;
use yii\db\Query;
use yii\helpers\BaseJson;
use ekalokman\AdminOci8\models\Assignment;
use ekalokman\AdminOci8\models\searchs\Assignment as AssignmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use ekalokman\AdminOci8\components\MenuHelper;
use yii\web\Response;
use yii\rbac\Item;
use common\components\ImoHelper;

/**
 * AssignmentController implements the CRUD actions for Assignment model.
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class AssignmentController extends Controller
{
    public $userClassName;
    public $idField = 'id';
    public $usernameField = 'username';
    public $searchClass;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->userClassName === null) {
            $this->userClassName = Yii::$app->getUser()->identityClass;
            $this->userClassName = $this->userClassName ? : 'common\models\User';
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'assign' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Assignment models.
     * @return mixed
     */
    public function actionIndex()
    {

        $role_cps_admin=ImoHelper::getRole("CPS Administrator");
        $role_kcd_admin=ImoHelper::getRole("Kulliyyah Administrator");

        if ($this->searchClass === null) {
            $searchModel = new AssignmentSearch;
        } else {
            $class = $this->searchClass;
            $searchModel = new $class;
        }

        if($role_cps_admin){
            $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams(), $this->userClassName, $this->usernameField);
        }else{
            $dataProvider = $searchModel->searchbykcd(\Yii::$app->request->getQueryParams(), $this->userClassName, $this->usernameField);
        }

        // $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams(), $this->userClassName, $this->usernameField);

        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'idField' => $this->idField,
                'usernameField' => $this->usernameField,
        ]);
    }

    /**
     * Displays a single Assignment model.
     * @param  integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $authManager = Yii::$app->authManager;
        $avaliable = [];
        $assigned = [];

        $getRolesByUser = (new Query())
                ->select(['QUEST.QST_IMON_AUTH_ITEM.*'])  // Selecting all columns from QST_IMON_AUTH_ITEM
                ->from('QUEST.QST_IMON_AUTH_ITEM')
                ->innerJoin('QUEST.QST_IMON_AUTH_ASSIGNMENT', 'QUEST.QST_IMON_AUTH_ASSIGNMENT."ITEM_NAME" = QUEST.QST_IMON_AUTH_ITEM."NAME"')
                ->where([
                    'QUEST.QST_IMON_AUTH_ASSIGNMENT.USER_ID' => $id,
                    'QUEST.QST_IMON_AUTH_ITEM.TYPE' => 1
                ]);

        $getRolesByUser = $getRolesByUser->all(Yii::$app->db);
        
        // $getRolesByUser = $authManager->getRolesByUser($id);

        // echo BaseJson::encode($getRolesByUser['TYPE']);exit;

        foreach ($getRolesByUser as $role) {
            // echo BaseJson::encode($role);exit;
            $type = $role['TYPE'];
            $assigned[$type == Item::TYPE_ROLE ? 'Roles' : 'Permissions'][$role['NAME']] = $role['NAME'];
        }

        $getRoles = (new Query())
                ->select(['QUEST.QST_IMON_AUTH_ITEM.*'])
                ->from('QUEST.QST_IMON_AUTH_ITEM')
                ->where([
                    'QUEST.QST_IMON_AUTH_ITEM.TYPE' => 1
                ]);

        $getRoles = $getRoles->all(Yii::$app->db);

        foreach ($getRoles as $role) {
            if (!isset($assigned['Roles'][$role['NAME']])) {
                $avaliable['Roles'][$role['NAME']] = $role['NAME'];
            }
        }

        $getPermissions = (new Query())
                ->select(['QUEST.QST_IMON_AUTH_ITEM.*']) 
                ->from('QUEST.QST_IMON_AUTH_ITEM')
                ->where([
                    'QUEST.QST_IMON_AUTH_ITEM.TYPE' => 2
                ]);

        $getPermissions = $getPermissions->all(Yii::$app->db);

        foreach ($getPermissions as $role) {
            if ($role['NAME'][0] !== '/' && !isset($assigned['Permissions'][$role['NAME']])) {
                $avaliable['Permissions'][$role['NAME']] = $role['NAME'];
            }
        }

        return $this->render('view', [
                'model' => $model,
                'avaliable' => $avaliable,
                'assigned' => $assigned,
                'idField' => $this->idField,
                'usernameField' => $this->usernameField,
        ]);
    }

    /**
     * Assign or revoke assignment to user
     * @param  integer $id
     * @param  string  $action
     * @return mixed
     */
    public function actionAssign($id, $action)
    {
        $post = Yii::$app->request->post();
        $roles = $post['roles'];
        $manager = Yii::$app->authManager;
        $error = [];

        if ($action == 'assign') {
            foreach ($roles as $name) {
                try {
                    // Fetch role
                    $item = (new Query())
                        ->select(['NAME']) 
                        ->from('QUEST.QST_IMON_AUTH_ITEM')
                        ->where(['TYPE' => 1, 'NAME' => $name])
                        ->one(); 

                    // Fetch permission
                    $getPermissions = (new Query())
                        ->select(['NAME']) 
                        ->from('QUEST.QST_IMON_AUTH_ITEM')
                        ->where(['TYPE' => 2, 'NAME' => $name])
                        ->one();

                    // Assign role or permission
                    $item = ($item ? $item['NAME'] : ($getPermissions ? $getPermissions['NAME'] : null));

                    if ($item) {
                        Yii::$app->db->createCommand()->insert('QUEST.QST_IMON_AUTH_ASSIGNMENT', [
                            'ITEM_NAME' => $item, 
                            'USER_ID' => $id,
                            'CREATED_AT' => time(),
                        ])->execute();
                    }
                } catch (\Exception $exc) {
                    $error[] = $exc->getMessage();
                }
            }
        } else {
            foreach ($roles as $name) {
                try {
                    // Fetch role
                    $item = (new Query())
                        ->select(['NAME'])
                        ->from('QUEST.QST_IMON_AUTH_ITEM')
                        ->where(['TYPE' => 1, 'NAME' => $name])
                        ->one();

                    // Fetch permission
                    $getPermissions = (new Query())
                        ->select(['NAME']) 
                        ->from('QUEST.QST_IMON_AUTH_ITEM')
                        ->where(['TYPE' => 2, 'NAME' => $name])
                        ->one();

                    // Assign role or permission
                    $item = ($item ? $item['NAME'] : ($getPermissions ? $getPermissions['NAME'] : null));

                    if ($item) {
                        Yii::$app->db->createCommand()->delete('QUEST.QST_IMON_AUTH_ASSIGNMENT', [
                            'ITEM_NAME' => $item,
                            'USER_ID' => $id,
                        ])->execute();
                    }
                } catch (\Exception $exc) {
                    $error[] = $exc->getMessage();
                }
            }
        }
        
        MenuHelper::invalidate();
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            $this->actionRoleSearch($id, 'avaliable', $post['search_av']),
            $this->actionRoleSearch($id, 'assigned', $post['search_asgn']),
            $error
        ];
    }

    /**
     * Search roles of user
     * @param  integer $id
     * @param  string  $target
     * @param  string  $term
     * @return string
     */
    public function actionRoleSearch($id, $target, $term = '')
    {   
        $role_cps_admin=ImoHelper::getRole("CPS Administrator");
        $role_kcd_admin=ImoHelper::getRole("Kulliyyah Administrator");

        $authManager = Yii::$app->authManager;
        $avaliable = [];
        $assigned = [];

        $getRolesByUser = (new Query())
                ->select(['QUEST.QST_IMON_AUTH_ITEM.*'])  // Selecting all columns from QST_IMON_AUTH_ITEM
                ->from('QUEST.QST_IMON_AUTH_ITEM')
                ->innerJoin('QUEST.QST_IMON_AUTH_ASSIGNMENT', 'QUEST.QST_IMON_AUTH_ASSIGNMENT."ITEM_NAME" = QUEST.QST_IMON_AUTH_ITEM."NAME"')
                ->where([
                    'QUEST.QST_IMON_AUTH_ASSIGNMENT.USER_ID' => $id,
                    'QUEST.QST_IMON_AUTH_ITEM.TYPE' => 1
                ]);

        $getRolesByUser = $getRolesByUser->all(Yii::$app->db);

        foreach ($getRolesByUser as $role) {
            $type = $role['TYPE'];
            $assigned[$type == Item::TYPE_ROLE ? 'Roles' : 'Permissions'][$role['NAME']] = $role['NAME'];
        }

        $getRoles = (new Query())
                ->select(['QUEST.QST_IMON_AUTH_ITEM.*'])
                ->from('QUEST.QST_IMON_AUTH_ITEM')
                ->where([
                    'QUEST.QST_IMON_AUTH_ITEM.TYPE' => 1
                ]);

        $getRoles = $getRoles->all(Yii::$app->db);

        foreach ($getRoles as $role) {
            if (!isset($assigned['Roles'][$role['NAME']])) {
                $avaliable['Roles'][$role['NAME']] = $role['NAME'];
            }
        }

        $getPermissions = (new Query())
                ->select(['QUEST.QST_IMON_AUTH_ITEM.*']) 
                ->from('QUEST.QST_IMON_AUTH_ITEM')
                ->where([
                    'QUEST.QST_IMON_AUTH_ITEM.TYPE' => 2
                ]);

        $getPermissions = $getPermissions->all(Yii::$app->db);

        foreach ($getPermissions as $role) {
            if ($role['NAME'][0] !== '/' && !isset($assigned['Permissions'][$role['NAME']])) {
                $avaliable['Permissions'][$role['NAME']] = $role['NAME'];
            }
        }
        if($role_cps_admin){
            $avaliable=$avaliable;
        }else{
            $remove=array('CPS Administrator');
            $new_array=array_diff($avaliable['Roles'],$remove);
            $avaliable['Roles']=$new_array;
            $avaliable['Permissions']=array( );
        }

        $result = [];
        $var = ${$target};
        if (!empty($term)) {
            foreach (['Roles', 'Permissions'] as $type) {
                if (isset($var[$type])) {
                    foreach ($var[$type] as $role) {
                        if (strpos($role, $term) !== false) {
                            $result[$type][$role] = $role;
                        }
                    }
                }
            }
        } else {
            $result = $var;
        }

        return Html::renderSelectOptions('', $result);
    }

    /**
     * Finds the Assignment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param  integer $id
     * @return Assignment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $class = $this->userClassName;
        if (($model = $class::findIdentity($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}