# 至简PHP项目(API)文档生成工具
这是一个独立的PHP项目文档生成工具。

## 官网地址
至简PHP开源框架官网：[http://www.php32.com](http://www.php32.com)

至简PHP项目(API)文档生成工具官网：[http://www.php32.com/doc](http://www.php32.com/doc)

## 安装说明
1. 下载程序
2. 解压程序，并给程序配置PHP环境(Apache需要开启rewrite_module)；请注意，程序必须存放在根目录。
3. 访问程序并登陆(初始账户: ```admin```、 密码: ```zhijianphp``` ，可以在 ```/config/conf.php``` 中修改和添加账户密码)
4. 设置相关信息，如下
	1. 文档URL：文档的访问地址
	2. 文档路径：文档的存放路径(绝对路径，Windows下请连带盘符一起填入)
	3. 项目URL：项目的访问地址
	4. 项目路径：需要生成文档的文件夹路径(绝对路径，Windows下请连带盘符一起填入)
	5. 开启登录验证：勾选后访问文档需要登陆，默认用户名: ```admin```、 密码: ```zhijianphp``` ，可以在 ```/libraries/doc/temp/login.php``` 中修改和添加账户密码
	6. 统计私有方法：勾选后将统计项目中的访问属性为 ```private``` 的方法
	7. 统计受保护的方法：勾选后将统计项目中的访问属性为 ```protected``` 的方法
	8. 开启接口测试模式：勾选后配置项会多出表单地址、链接秘钥两个选项：
       + 表单地址：开启接口测试模式后，程序自动会将文档要求参数传递到此地址中。 您可以在此地址中将文档参数按照项目要求进行签名和封装。我们会将文档要求参数存放在 ```params``` 数组中，并同时传递 ```relative_path``` 、```class``` 、```method``` 、```http_method``` 、```debug_sign``` 参数；它们分别代表：接口相对路径、类名、方法名、HTTP请求方式和链接秘钥(用于确认请求是受信任的)
       + 链接秘钥：秘钥会在提交数据时同时发送，你可以比对秘钥以确保请求来源安全可靠
## 文档注释语法说明
```

    @copyright 	版权信息
	@version 	版本信息
    @link		链接地址
    @date		时间
    @params		参数(格式：@params 数据类型 参数名[带$符号] 参数介绍[如果是数组可以换行写明具体要求])
    @scene		应用场景
    @method		HTTP请求方式
    @author		作者
    @url		访问链接[默认程序会自动生成，并兼容多数框架，如果非段式URL和个性化框架可自行定义访问链接]
    @name		方法名
    @brief		摘要[默认情况下如非@指明的注释都将归纳在摘要里]
    @return		返回结果
    @throws		异常(格式：@throws 状态码 异常说明文字)

```
## 其它说明
1. 文档注释中大括号表示关联数组，中括号表示索引数组，小括号表示枚举，示例：
```

@return JSON
[
	{
    	type	: 类型
        (
        	1	: 类型1
            2	: 类型2
        )
    },
    {
    	name	: 名称
    }
]
以上代码我们假设type的结果是1，name的结果是空；那么翻译之后：
JSON格式字符串：[{"type":1},{"name":""}]
PHP数组：array(array('type'=>1), array('name'=>''))

```
2. 你可以在 ```/libraries/doc/temp/login.php``` 中修改文档的登录验证方式
3. 你可以在 ```/libraries/doc/temp/form.php``` 中增加或者修改接口测试表单所传递的参数
4. 你可以在 ```/controller/Home.php``` 中修改程序的登录验证方式
## 完整示例
```

/**
 * Copyright (c) 2015, www.php32.com Inc. All Rights Reserved
 * 至简PHP项目文档生成器
 * @link http://www.php32.com/doc
 * @date 2015-05-30
 * @version 4.0
 * @author admin@php32.com
 */
 
 class Test{
 	/**
     * @name 测试方法
     * @scene README演示时使用
     * @method GET
     * @author admin@php32.com
     * @params string 	$one 第一个参数
     * @return JSON
     * {
     * 		name	: 名字,
     * 		type	: 类型
     * }
     * @throws param_one_null 第一个参数为空
     */
	function index(){
    	if(empty($_GET['one'])){
        	throw new Exception('param_one_null');
        }
    	echo json_encode(array('name'=>'','type'=>''));
    } 	
 }

```
 
## 使用协议
开源是一种美德，使用和支持开源的产品更是一种美德。

使用本程序是完全免费的，版权归至简PHP开源框架官方([http://www.php32.com](http://www.php32.com))所拥有。

如果你修改了本程序，并且是非针对你的项目进行的个性化修改；我们希望你可以将修改后代码反馈给我们，我们将持续优化和增加本程序的功能。

如果你在使用中发现了BUG，也请及时的反馈给我们。当然，如果是带着修改后的代码一起反馈将是一件非常美好的事情。

我们希望不论你是作为一名使用者还是参与者，都能为开源力量贡献一份自己的力量。

如你所见，本程序的代码写的也不是非常的牛逼；所以请不要小觑了自己的力量。

你可以通过admin#php32.com(#替换成@)联系到我们，也可以在我们的官网：[http://www.php32.com](http://www.php32.com)留言给我们。
