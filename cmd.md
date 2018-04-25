// 清除缓存命令 ` php think clear`


// 生成数据表字段缓存  这样可以优化下查询  ` php think optimize:schema`

// 生成类库的映射文件  提高系统的加载性能   `php think optimize:autoload`

// 生成控制器        `php think make:controller index/Index  --plain`

// 生成模型          `php think make:model      index/Index`

// 因为在系统中可能会需要一些大批量数据出来,避免宕机,应该用命令行来

// 下面记录下自定义命令行  对应文件为 **\application\console\controller**

