# TencentCaptcha
腾讯验证码
## 适合范围
thinkphp5.1.x 专用

## 使用说明

* php版本 >=7.0.0

### key申请
传送门 [https://open.captcha.qq.com/](https://open.captcha.qq.com/)

> 在控制器里

```php
    //腾讯验证码
    $Captcha = new \static7\Captcha(['aid'=>'xxx',''=>'xxxx']);
```

> 前端页面配置  
> 我是用的 layui 写的一个测试例子  
> 具体参考页面 [https://007.qq.com/captcha/#/gettingStart](https://007.qq.com/captcha/#/gettingStart)
```html
<form class="layui-form" action="{:Url::build('Example/submit')}" method="post">
    //表单代码省略 ...
    
    <div class="layui-form-item">
        <div class="layui-input-block">
            <input type="hidden" name="ticket" value="">
            <input type="hidden" name="randstr" value="">
            <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
        </div>
    </div>
</form>
```
```javascript 1.6
<script src="https://ssl.captcha.qq.com/TCaptcha.js"></script>
<script type="text/javascript">
    layui.use(['jquery', 'form'], function () {
        var captcha = new TencentCaptcha('aid', function(result) {
            console.log(result);
            if (result.ret===2){
                layui.layer.msg('你干嘛关闭呢?');
                return false;
            }
            layui.$("input[name='ticket']").val(result.ticket);
            layui.$("input[name='randstr']").val(result.randstr);
            var form = layui.$("form");
            layui.$.post(form.attr("action"), form.serialize(), function (data){
                if (data.code===0){
                    layui.layer.msg(data.msg);
                    return false;
                }
                layui.layer.msg(data.msg);
            });
        });
        layui.form.on('submit(demo1)', function () {
            captcha.show();
            return false;
        });
    });
</script>
```

> 控制器 Example里的 submit 方法

```php
    /**
     * Submit
     * @author staitc7 <static7@qq.com>
     * @return mixed
     * @throws \think\Exception
     */
    public function submit()
    {
        $param=$this->app->request->param();
        $Captcha=new \static7\Captcha();
        // 两种方式 一种
//        $result=$Captcha->setRandstr($param['randstr'])->setTicket($param['ticket'])->verify();
        //或者
        $result=$Captcha->verify($param['ticket'],$param['randstr']);
        if ((int)$result['response']===0){
            return $this->result($param,0,$result['err_msg']);
        }
        return $this->result($param,1,'成功');
    }
```






