<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/18 0018
 * Time: 10:32
 * Comment:
 */
namespace app\shop\controller;
use app\common\controller\Base;
use think\Db;
use think\Request;
class Advertise extends Base{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->modelName = 'Advertisement';
        $this->theme = '广告位';
        $this->order = 'a.sort desc';
        $this->field = 'a.id,a.poster,a.name,a.url,a.type,a.position,a.sort,a.isBlank,b.savePath as posterPath,a.created_at';
        $this->join = [['File b','a.poster=b.id','left']];
        $this->model = model($this->modelName);
    }


}