<?php

namespace app\common\controller;

use think\Controller;
use think\Request;
use think\captcha\Captcha;

/*
 * 基层控制层,所有公共操作函数都在此函数
 * */

class Base extends Controller
{
    public $roleId = null;
    public $menuIds = null;
    public $modelName = null;
    public $model = null;
    public $theme = null;
    public $condition = [];
    public $field = '*';
    public $rowField = '*';
    public $rowJoin = [];
    public $join = [];
    public $order = '';

    // 用户权限全局检查
    public function _initialize()
    {
        // 用户访问模块
        $module = Request::instance()->module();
        $ctr    = Request::instance()->controller();
        $this->assign('ctrName',$ctr);
        $this->assign('fromDataUrl',url($module.'/'.$ctr.'/getRowData'));
        $this->assign('fromSaveUrl',url($module.'/'.$ctr.'/store'.$ctr));
        $this->assign('delDataUrl',url($module.'/'.$ctr.'/deleteToTable'));
        $this->assign('tableDataUrl',url($module.'/'.$ctr.'/getTableData'));
        switch ($module) {
            // 后台管理模块
            case 'admin':
                // 用户ID
                $userId = session('userId');
                // 加盟商ID
                $franchiseeId = session('franchiseeId');
                // 用户基本信息数组
                $userInfo = session('user');
                if ((empty($userId) || empty($userInfo))) {
                    $loginUrl = url('/user/Login/index');
                    $this->redirect($loginUrl);
                    return;
                } else {
                    $this->roleId = session("user.roles_id");
                    $menus = model("Roles")->get(session("user.roles_id"))->toArray();
                    $this->menuIds = $menus['menu_ids'];
                }
                break;
            // PC商城模块
            case 'pcshop':
                // 分类数据 以及导航数据  因为现在导航都是显示的分类
                $this->getPcshopCommonData();
                break;
        }

    }

    /**
     * 商户模块用户未登录前置函数
     */
    public function illegalPcshopUser()
    {
        if(empty(session("pcshopUserId"))){
            $strUrl = url('/pcshop/User/login');
            $this->redirect($strUrl);
        }
    }


    /**
     * @param $name
     * @return \think\response\View
     */
    public function _empty($name)
    {
        return view($name);
    }

    public function getTableData()
    {
        $list = model($this->modelName)->getBootstrapeTable($this->condition, $this->field, $this->join, $this->order);
        return $list;
    }

    /**
     * 新增/编辑数据
     * @return array
     */
    public function saveToTable($extraData=[])
    {
        $request = Request::instance();
        if (strtolower($request->method()) == 'post') {
            try {
                $data = $request->post();
                $data = array_merge($data,$extraData);
                if(!empty($data['test'])){
                    unset($data['test']);
                }
                // 校验
                if (empty($data['id'])) {
                    $name = '新增' . $this->theme;
                    $code = '';
                    $valiMsg = $this->validate($data, $this->modelName . '.insert');
                } else {
                    if(!empty($data['actionName'])){
                        $name = $data['actionName'];
                        $code = $data['actionCode'];
                        $valiMsg = $this->validate($data, $this->modelName . '.'.$code);
                    }else{
                        $name = '编辑' . $this->theme;;
                        $code = '';
                        $valiMsg = $this->validate($data, $this->modelName . '.update');
                    }
                }
                if ($valiMsg !== true) {
                    throw new \Exception($valiMsg);
                }
                // 控制下level
                // 获取父级level
                if (isset($data['pid']) && $data['pid'] > 0) {
                    $parentLevel = model($this->modelName)->where(['id' => $data['pid'], 'deleted_at' => null])->column('level');
                    $data['level'] = ++$parentLevel[0];
                }
                $result = model($this->modelName)->saveData($data, $this->modelName, $name, $code);
                if ($result['code'] == 0) {
                    throw new \Exception($result['msg']);
                }
                return $result;
            } catch (\Exception $e) {
                mdLog($e);
                return ['code' => 0, 'msg' => $e->getMessage()];
            }
        } else {
            return ['code' => 0, 'msg' => '非法请求' . $request->method()];
        }
    }


    /**
     * @param Request $request
     * @return array
     */
    public function getRowData(request $request)
    {
        $id = $request->only('id');
        $id = $id['id'];
        if (!empty($id)) {
            $result = $this->model->getRow(['a.id'=>$id], $this->rowField, $this->rowJoin, $this->order);
            if($result['code'] == 0){
                return ['code' => 0, 'msg' => $this->theme . '数据不存在'.$result['msg']];
            }
            return $result;
        }
        return ['code' => 0, 'msg' => $this->theme . '数据不存在'];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function deleteToTable(request $request)
    {
        try {
            $levelModel = ['Menus', 'Roles', 'Category'];
            $data = $request->only('id');
            if (empty($data['id'])) {
                throw new \Exception('数据不存在');
            }
            if (in_array($this->modelName, $levelModel)) {
                $list = model($this->modelName)->getCommonCollection(['pid' => $data['id']], 'id');
                if ($list['code'] == 0) {
                    throw new \Exception('父级' . $this->theme . '出错');
                }
                if (!empty($list['data'])) {
                    throw new \Exception('存在子' . $this->theme . ',不可删除');
                }
            }
            $result = model($this->modelName)->deleteData($data['id'], 'Menus', '删除' . $this->theme, '1002001');
            if ($result['code'] == 0) {
                throw new \Exception('删除失败' . $result['msg']);
            }
            return ['code' => 1, 'msg' => $this->theme . '删除成功', 'data' => $data['id']];
        } catch (\Exception $e) {
            mdLog($e);
            return ['code' => 0, 'msg' => $e->getMessage(), 'data' => []];
        }
    }

    /*
       * 获取商城公共部分数据
       * 分类数据
       * 导航数据
       * SEO信息
       * 商城基本信息
       * 商城帮助数据
   */
    private function getPcshopCommonData()
    {
        // 分类数据 导航数据
        // 获取导航显示的分类 start
        // 分类tree end
        $navInfo = model('Category')->getCateNavTree();
        $this->assign('navCategory',$navInfo['navBlock']);
        $this->assign('category',$navInfo['cateTree']);
        // 帮助信息 start
        $cataLog = model('Catalog')->getDataList([],'a.id,a.parent_id,a.title');
        $cataLog = list_to_tree($cataLog['data'],'id','parent_id','subLog');
        $this->assign('cataLog',$cataLog);
        // 帮助信息 end

        // 获取SEO信息 现在只是简单的做一下 start
        $seo = model('Config')->getConfigById(1);
        $this->assign('seo',$seo);
        // 获取SEO信息 end

        // 获取基本信息 现在只是简单的做一下 start
        $baseInfo['address'] = '滨江俊园店地址：昆明市盘龙区白云路滨江俊园3-03号商铺';
        $baseInfo['telphone'] = '0871-68523387';
        $baseInfo['worktime'] = '周一至周日:9:00-21:30';
        $baseInfo['copyRight'] = 'Copyright © 2012-2014 三九妈咪网 版权所有';
        $baseInfo['email'] = '999@mama999.com';
        $baseInfo['qq'] = '761243073';
        $this->assign('baseInfo',$baseInfo);
        // 获取基本信息 end

        // 获取用户的购物车商品数量
        if($userId = session('pcshopUserId')){
            $cartsNumber = model('Carts')->where(['userId'=>$userId])->count('id');
        }
        $this->assign('cartsNumber',isset($cartsNumber) ? $cartsNumber : 0);
    }

    public function getColumn($model,$condition=[],$column)
    {
        try {
            $result = model($model)->getColumn($condition,$column);
            return $result;
        } catch (\Exception $e) {
            mdLog($e);
            return ['code' => 0, 'msg' => $e->getMessage(), 'data' => []];
        }
    }


    public function fullyDel($field,$ids)
    {
        return $this->model->fullyDelete($field,$ids);
    }

    public function fullyStore()
    {
        $data = $this->request->post();
        return $this->model->store($data,$this->theme);
    }

    public function getJqPage()
    {
        $data = $this->model->getJQPage([],$this->field,[],$this->order);
        return $data;
    }

    public function dataList()
    {
        return $this->model->listData();
    }

    /**
     * 生产验证码
     * @param string $type
     * @param int $fontSize
     * @param int $length
     * @param bool $useNoise
     * @return \think\Response
     */
    public function getCaptChar($type="num",$fontSize=30,$length=6,$useNoise=false)
    {
        switch($type){
            case 'num':
                $rType = '0123456789';
                break;
            case 'string':
                $rType = '';
                break;
        }
        $config =    [
            'codeSet'=> $rType,
            'useZh'=>   false,
            // 验证码字体大小
            'fontSize'    =>    $fontSize,
            // 验证码位数
            'length'      =>    $length,
            // 关闭验证码杂点
            'useNoise'    =>    $useNoise,
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }
}
