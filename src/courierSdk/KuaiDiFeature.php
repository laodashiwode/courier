<?php

namespace courierSkd;
/**
 * 快递100Feature
 *
 * @package App\Common\Feature
 */
class KuaiDiFeature
{
    //客户授权key
    public $url = config('courier.courier_url');

    //客户授权key
    public $key = config('courier.courier_key');

    //快递100分配公司编号customer
    public $customer = config('courier.courier_customer');

    //返回数据格式
    public $schema = config('courier.courier_schema');

    //返回数据格式。0：json（默认），1：xml，2：html，3：text
    public $show = config('courier.courier_show');

    //返回结果排序方式。desc：降序（默认），asc：升序
    public $order = config('courier.courier_order');


    /**
     * 订阅请求
     * @param array (
     * 'company' => 'yunda',            //快递公司编码
     * 'number' => '3950055201640',    //快递单号
     * 'from' => '',                    //出发地城市（可选）
     * 'to' => '',                        //目的地城市（可选）
     * 'key' => $key,                    //客户授权key
     * 'parameters' => array (
     * 'callbackurl' => '',        //回调地址地址。如果需要在推送信息回传自己业务参数，可以在回调地址URL后面拼接上去，如：http://www.xxxxx.com/callback?orderId=123
     * 'salt' => '',                //加密串（可选）
     * 'resultv2' => '1',            //行政区域解析（可选）
     * 'autoCom' => '0',            //单号智能识别（可选）
     * 'interCom' => '0',            //开启国际版（可选）
     * 'departureCountry' => '',    //出发国（可选）
     * 'departureCom' => '',        //出发国快递公司编码（可选）
     * 'destinationCountry' => '',    //目的国（可选）
     * 'destinationCom' => '',        //目的国快递公司编码（可选）
     * 'phone' => ''                //手机号（可选）
     * )
     * );
     * @return array(
     * result => 'true',
     * returnCode => '200',
     * 'message' => '提交成功'
     * )
     * @author lijing@3ncto.com
     */
    public function postSubscribe($params)
    {
        $post_data = [];
        $post_data["schema"] = $this->schema;
        $post_data["param"] = json_encode($params);
        return $this->sendPost($this->url, $post_data);
    }

    /**
     * 实时查询
     * @param array (
     * 'customer' => '',
     * 'com' => 'yunda',            //快递公司编码
     * 'num' => '3950055201640',    //快递单号
     * 'phone' => '',                //手机号（可选）
     * 'from' => '',                //出发地城市（可选）
     * 'to' => '',                    //目的地城市（可选）
     * 'resultv2' => '1'            //开启行政区域解析（可选）
     * );
     * @return array (
     * "message" => "ok",
     * "state" => "0",
     * "status" => "200",
     * "condition" => "F00",
     * "ischeck" => "0",
     * "com" => "yuantong",
     * "nu" => "V030344422",
     * "data" => [
     * [
     * "context" => "上海分拨中心/装件入车扫描 ",
     * "time" => "2012-08-28 16:33:19",
     * "ftime" => "2012-08-28 16:33:19",
     * ]
     * ]
     * )
     * @author lijing@3ncto.com
     */
    public function synQuery($params, $show = null, $order = null)
    {
        $post_data = [];
        $post_data["customer"] = $this->customer;
        $post_data["param"]["show"] = !empty($show) ? $show : $this->show;
        $post_data["param"]["order"] = !empty($order) ? $order : $this->order;
        $post_data["param"] = json_encode($params);
        $sign = md5($post_data["param"] . $this->key . $post_data["customer"]);
        $post_data["sign"] = strtoupper($sign);
        return $this->sendPost($this->url, $post_data);
    }

    /**
     * curl post
     *
     * @author lijing@3ncto.com
     * @param string $url
     * @param array $post_data
     * @return array
     */
    public function sendPost($url, $post_data)
    {
        $params = "";
        foreach ($post_data as $k => $v) {
            $params .= "$k=" . urlencode($v) . "&";        //默认UTF-8编码格式
        }
        $post_data = substr($params, 0, -1);

        //发送post请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $data = str_replace("\"", '"', $result);
        $data = json_decode($data);

        return $data;
    }
}
